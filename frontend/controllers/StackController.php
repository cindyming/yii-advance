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
use yii\helpers\Html;
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
    public function actionValidatebuy()
    {
        $model = new StackTransaction();
        $stack = Stack::findOne(Yii::$app->request->get('id'));


        if ($model->load(Yii::$app->request->post())) {
            $data = Yii::$app->request->post();
            $result = ActiveForm::validate($model);
            $model->price = $stack->price;
            $model->member_id = Yii::$app->user->identity->id;
            $model->stack_id = $stack->id;
            $model->type = 0;
            $model->total_price = $model->price * $model->volume;

            if ($data['StackTransaction']['password2'] && Yii::$app->user->identity->validatePassword2($data['StackTransaction']['password2'])) {
                $model->addError('password2', '第二密码不正确, 请确认后重新输入.');
            }
            if ((($model->account_type == 1) && Yii::$app->user->identity->finance_fund < $model->total_price) ||
                (($model->account_type == 2) && Yii::$app->user->identity->stack_fund < $model->total_price)) {
                if (Yii::$app->user->identity->finance_fund < $model->total_price) {
                    $model->addError('volume', '账户余额不足. 理财基金:.' . Yii::$app->user->identity->finance_fund . '. 购股账户:'. Yii::$app->user->identity->stack_fund);
                }
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[Html::getInputId($model, $attribute)] = $errors;
            }
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));

        } else {
            echo json_encode(array());
        }
        Yii::$app->end();

    }

    public function actionBuy()
    {
        $model = new StackTransaction();
        $stack = Stack::findOne(Yii::$app->request->get('id'));
        $memberStack = Yii::$app->user->identity->getMemberStack($stack->id);
        if (!$memberStack) {
            $memberStack = new MemberStack();
        }
        $open = false;

        if (Date::isWorkingDay()||true) {
            if (Date::isWorkingTime()||true) {
                $open = true;
                if ($model->load(Yii::$app->request->post())) {
                    $data = Yii::$app->request->post();
                    $model->price = $stack->price;
                    $model->member_id = Yii::$app->user->identity->id;
                    $model->stack_id = $stack->id;
                    $model->type = 0;
                    $model->total_price = $model->price * $model->volume;
                    $validate = true;;
                    if (!Yii::$app->user->identity->validatePassword2($data['StackTransaction']['password2'])) {
                        $validate = false;
                        $model->addError('password2', '第二密码不正确, 请确认后重新输入.');
                    }
                    if ((($model->account_type == 1) && Yii::$app->user->identity->finance_fund < $model->total_price) ||
                        (($model->account_type == 2) && Yii::$app->user->identity->stack_fund < $model->total_price)) {
                            $validate = false;
                            $model->addError('volume', '账户余额不足. 理财基金:.' . Yii::$app->user->identity->finance_fund . '. 购股账户:'. Yii::$app->user->identity->stack_fund);
                    }
                    if ($validate) {

                        $memberStack = MemberStack::getMemberStack($model);
                        $member = Member::findOne($model->member_id);

                        if ($model->account_type == 1) {
                            $member->finance_fund -= $model->total_price;
                            $outRecord = OutRecord::prepareModelForBuyStack($model->member_id, $model->total_price, $member->finance_fund, 1);
                        } else {
                            $member->stack_fund -= $model->total_price;
                            $outRecord = OutRecord::prepareModelForBuyStack($model->member_id, $model->total_price, $member->stack_fund, 2);
                        }
                        $connection = Yii::$app->db;
                        try {
                            $transaction = $connection->beginTransaction();
                            $success = false;
                            if ( $model->save() && $memberStack->save() && $member->save() &&  $outRecord->save()) {
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
                                Yii::error(json_encode($outRecord->getErrors()));
                                $transaction->rollback();
                            }

                        } catch (Exception $e) {
                        }
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
            'memberStack' => $memberStack,
            'open' => $open
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
