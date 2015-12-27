<?php

namespace frontend\controllers;

use common\models\Date;
use common\models\Member;
use common\models\MemberStack;
use common\models\OutRecord;
use common\models\search\MemberStackSearch;
use common\models\search\StackTransactionSearch;
use common\models\search\StackTrendsSearch;
use common\models\StackTransaction;
use common\models\StackTrends;
use Yii;
use common\models\Stack;
use common\models\search\StackSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use backend\models\User;
use yii\widgets\ActiveForm;
use yii\web\Response;


class StackController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'ruleConfig' => [
                    'class' => \frontend\components\AccessRule::className(),
                ],
                'rules' => [
                    [
                        'actions' => ['buy', 'validatebuy','sell', 'index', 'trends', 'fund', 'transactions'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actionBuy()
    {
        $model = new StackTransaction();
        $stack = Stack::findOne(Yii::$app->request->get('id'));
        $memberStack = Yii::$app->user->identity->getMemberStack($stack->id);
        if (!$memberStack) {
            $memberStack = new MemberStack();
        }

        if (Date::isWorkingDay()) {
            if (Date::isWorkingTime()) {
                if ($model->load(Yii::$app->request->post())) {
                    $data = Yii::$app->request->post();
                    $validate = true;;
                    if (Yii::$app->user->identity->validatePassword2($data['StackTransaction']['password2'])) {
                        $validate = false;
                        $model->addError('password2', '第二密码不正确, 请确认后重新输入.');
                    }
                    if ($validate) {
                        $model->price = $stack->price;
                        $model->member_id = Yii::$app->user->identity->id;
                        $model->stack_id = $stack->id;
                        $model->type = 0;
                        $model->total_price = $model->price * $model->volume;
                        $memberStack = MemberStack::getMemberStack($model);
                        $member = Member::findOne($model->member_id);
                        $stackFund = $member->stack_fund;
                        $financeMisFund = 0;
                        $stackOutRecord = null;
                        $financeOutRecord = null;
                        if ($stackFund > $model->total_price) {
                            $member->stack_fund -= $model->total_price;
                            $stackOutRecord = OutRecord::prepareModelForBuyStack($model->member_id, $model->total_price, $member->stack_fund, 2);
                        } else if ($member->stack_fund > 0) {
                            $financeMisFund = $model->total_price - $member->stack_fund;
                            $member->stack_fund = 0;
                            $stackOutRecord = OutRecord::prepareModelForBuyStack($model->member_id, ($model->total_price - $financeMisFund), 0, 2);

                        } else {
                            $financeMisFund = $model->total_price;
                        }
                        $financeOutRecord = null;
                        if ($financeMisFund) {
                            $member->finance_fund -= $financeMisFund;
                            $financeOutRecord = OutRecord::prepareModelForBuyStack($model->member_id, $financeMisFund, $member->finance_fund, 1);
                        }

                        $connection = Yii::$app->db;
                        try {
                            $transaction = $connection->beginTransaction();
                            $success = false;
                            if ($stackOutRecord && $financeOutRecord && $model->save() && $memberStack->save() && $member->save() && $stackOutRecord->save() && $financeOutRecord->save()) {
                                $success = true;
                            } elseif ($financeOutRecord && $model->save() && $memberStack->save() && $member->save() && $financeOutRecord->save()) {
                                $success = true;
                            } elseif ($stackOutRecord && $model->save() && $memberStack->save() && $member->save() && $stackOutRecord->save()) {
                                $success = true;
                            }
                            if ($success) {
                                $transaction->commit();
                                return $this->redirect(['transactions']);
                            } else {
                                Yii::error('Stack Buy Failed');
                                Yii::error(json_encode($model->getErrors()));
                                Yii::error(json_encode($memberStack->getErrors()));
                                Yii::error(json_encode($member->getErrors()));
                                if ($stackOutRecord) {
                                    Yii::error(json_encode($stackOutRecord->getErrors()));
                                }
                                if ($financeOutRecord) {
                                    Yii::error(json_encode($financeOutRecord->getErrors()));
                                }
                                $transaction->rollback();
                            }

                        } catch (Exception $e) {
                        }
                    } else {
                        $model->validate();
                    }
                }
            } else {
                Yii::$app->session->setFlash('danger', '非交易时间. 早上10:00 ~ 12:00. 下午2:00 ~ 4:00');
            }
        } else {
            Yii::$app->session->setFlash('danger', '对不起,非交易日不能进行交易!');
        }

        return $this->render('buy', [
            'model' => $model,
            'stack' => $stack,
            'memberStack' => $memberStack
        ]);
    }

    public function actionSell($id)
    {
        $stack = Stack::findOne($id);
        $model = new StackTransaction();
        $memberStack = Yii::$app->user->identity->getMemberStack($stack->id);
        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            $validate = true;
            if (!Yii::$app->user->identity->validatePassword2($data['StackTransaction']['password2'])) {
                $validate = false;
                $model->addError('password2', '第二密码不正确, 请确认后重新输入.');
            }
            if (!$model->checkSellVolume($memberStack, $model->volume)) {
                $validate = false;
            }
            if ($validate) {
                $model->price = $stack->price;
                $model->member_id = Yii::$app->user->identity->id;
                $model->stack_id = $stack->id;
                $model->type = 1;
                $model->total_price = $model->price * $model->volume;
                $memberStack->sell_volume -= $model->volume;
                $memberStack->lock_volume += $model->volume;

                $connection = Yii::$app->db;
                try {
                    $transaction = $connection->beginTransaction();
                    if ($model->save() && $memberStack->save()) {
                        $transaction->commit();
                        return $this->redirect(['transactions']);
                    } else {
                        Yii::error('Sell Stack Failed');
                        Yii::error( json_encode($model->getErrors()));
                        Yii::error( json_encode($memberStack->getErrors()));
                        $transaction->rollback();
                    }
                } catch (Exception $e) {

                }
            }
        }
        return $this->render('sell', [
            'model' => $model,
            'stack' => $stack,
            'memberStack' => $memberStack
        ]);
    }


    public function actionIndex()
    {
        $searchModel = new StackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTrends()
    {
        $searchModel = new StackTrendsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('trends', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFund()
    {
        $searchModel = new MemberStackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('fund', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionTransactions()
    {
        $searchModel = new StackTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('transactions', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

}
