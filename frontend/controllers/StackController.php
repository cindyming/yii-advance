<?php

namespace frontend\controllers;

use common\models\Date;
use common\models\InRecord;
use common\models\JLock;
use common\models\Member;
use common\models\MemberStack;
use common\models\OutRecord;
use common\models\search\MemberStackSearch;
use common\models\search\StackTransactionSearch;
use common\models\search\StackTrendsSearch;
use common\models\StackTransaction;
use common\models\StackTrends;
use common\models\System;
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
                        'actions' => ['buy', 'validatebuy','sell', 'index', 'trends', 'fund', 'transactions', 'unlock', 'prices'],
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
//
//            'pageCache' => [
//                'class' => 'yii\filters\PageCache',
//                'only' => ['index', 'trends'],
//                'duration' => 360,
//                'dependency' => [
//                    'class' => 'yii\caching\DbDependency',
//                    'sql' => 'SELECT COUNT(*) FROM stack_trends',
//                ],
//                'variations' => [
//                    Yii::$app->request->get('page', 1),
//                    Yii::$app->request->get('StackSearch', array()),
//                    Yii::$app->request->get('StackTrendsSearch', array())
//                ]
//            ],
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
        if(!Yii::$app->user->identity->canBuyStock()) {
            Yii::$app->session->setFlash('danger', '账号没有购股权限, 请联系管理员.');
        } else if ($stack->status) {
            Yii::$app->session->setFlash('danger', '股票已锁定,请选择其它股票进行购买.');
        } else if (Date::isWorkingDay()) {
            if (Date::isWorkingTime()) {
                $open = true;
                $key = 'BUY' . Yii::$app->user->identity->id . $stack->id;
                if (!Yii::$app->cache->exists($key)) {
                    Yii::$app->cache->set($key, 1, 10);
                    if ($model->load(Yii::$app->request->post())) {
                        if ($model->account_type) {
                            $data = Yii::$app->request->post();
                            $model->price = $stack->price;
                            $model->member_id = Yii::$app->user->identity->id;
                            $model->stack_id = $stack->id;
                            $model->type = 0;
                            $model->total_price = $stack->price * $model->volume;
                            $validate = true;;
                            if (!Yii::$app->user->identity->validatePassword2($data['StackTransaction']['password2'])) {
                                $validate = false;
                                $model->addError('password2', '第二密码不正确, 请确认后重新输入.');
                            }
                            if ((($model->account_type == 1) && Yii::$app->user->identity->finance_fund < $model->total_price) ||
                                (($model->account_type == 2) && Yii::$app->user->identity->stack_fund < $model->total_price)
                            ) {
                                $validate = false;
                                $model->addError('volume', '账户余额不足. 理财基金:.' . Yii::$app->user->identity->finance_fund . '. 购股账户:' . Yii::$app->user->identity->stack_fund);
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
                                $outRecord->note = '股买[' . $stack->code . ']' . $model->volume . '股';
                                $connection = Yii::$app->db;
                                try {
                                    $transaction = $connection->beginTransaction();
                                    $success = false;
                                    if ($model->save() && $memberStack->save() && $member->save() && $outRecord->save()) {
                                        $success = true;
                                    }
                                    if ($success) {
                                        Yii::$app->session->setFlash('success', '购买成功');
                                        $transaction->commit();
                                        return $this->redirect(['transactions']);
                                    } else {
                                        Yii::error('Stack Buy Failed');
                                        Yii::$app->session->setFlash('danger', '购买失败,请稍后再试.');
                                        Yii::error(json_encode($model->getErrors()));
                                        Yii::error(json_encode($memberStack->getErrors()));
                                        Yii::error(json_encode($member->getErrors()));
                                        Yii::error(json_encode($outRecord->getErrors()));
                                        $transaction->rollback();
                                    }

                                } catch (Exception $e) {
                                }
                            }
                        } else {
                            $model->total_price = $stack->price * $model->volume;
                        }
                    }
                    Yii::$app->cache->delete($key);
                } else {
                    $show = false;
                    Yii::$app->session->setFlash('danger', '对不起,重复提交!');
                }
            } else {
                Yii::$app->session->setFlash('danger', '非交易时间. 早上10:00 ~ 12:30. 下午2:00 ~ 4:00');
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

    public function actionUnlock()
    {
        $id = Yii::$app->request->get('id');
        $stackTransaction = StackTransaction::findOne($id);
        $connection = Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();
            if ($stackTransaction->type == 0) {
                $stackTransaction->status = 1;
                $memberStack = $stackTransaction->getMemberStack()->one();
                $memberStack->sell_volume += $stackTransaction->volume;
                $memberStack->lock_volume -= $stackTransaction->volume;
                if ($memberStack->save() && $stackTransaction->save()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '交易自主解锁成功');
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '交易自主解锁失败, 请稍后再试或联系管理员');
                }
            } else {
                $stackTransaction->status = 1;
                $memberStack = $stackTransaction->getMemberStack()->one();
                $memberStack->lock_volume -= $stackTransaction->volume;
                $member = $stackTransaction->getMember()->one();
                $fee = round($stackTransaction->total_price * System::loadConfig('sell_fee_rate'), 2);
                $member->stack_fund += ($stackTransaction->total_price - $fee);
                $stackOutRecord = InRecord::prepareModelForSellStack($stackTransaction->member_id, ($stackTransaction->total_price - $fee), $member->stack_fund, $fee);
                $stackOutRecord->account_type = 2;
                $stackOutRecord->note = '自主解锁[' .$stackTransaction->created_at. ']出售股票[' . $stackTransaction->stack->code . ']' . $stackTransaction->volume . '股';
                if ($memberStack->save() && $stackTransaction->save() && $member->save() && $stackOutRecord->save()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '交易自主解锁成功');
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '交易自主解锁失败, 请稍后再试或联系管理员');
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $this->redirect(Yii::$app->request->referrer);
        return;
    }

    public function actionSell($id)
    {
        $stack = Stack::findOne($id);
        $model = new StackTransaction();
        $memberStack = Yii::$app->user->identity->getMemberStack($stack->id);
        $show = true;

        if(!Yii::$app->user->identity->canBuyStock()) {
            $show = false;
            Yii::$app->session->setFlash('danger', '账号没有购股权限, 请联系管理员.');
        } else if ($stack->status == 1) {
            $show = false;
            Yii::$app->session->setFlash('danger', '股票已锁定,请选择其它股票进行购买.');
        } else if (Date::isWorkingDay()) {
            if (Date::isWorkingTime()) {
                if ($model->load(Yii::$app->request->post())) {
                        $key = 'CELL' . Yii::$app->user->identity->id . $stack->id;
                        if (!Yii::$app->cache->exists($key)) {
                            Yii::$app->cache->set($key, 1, 10);
                            $memberStack = Yii::$app->user->identity->getMemberStack($stack->id);
                            if($model->account_type) {
                                $data = Yii::$app->request->post();
                                $validate = true;
                                $password = $data['StackTransaction']['password2'];
                                $password = (!is_string($password) || $password === '') ? 'a' : $password;
                                if (!Yii::$app->user->identity->validatePassword2($password)) {
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
                                    $model->total_price = $stack->price * $model->volume;
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
                                            Yii::error(json_encode($model->getErrors()));
                                            Yii::error(json_encode($memberStack->getErrors()));
                                            $transaction->rollback();
                                        }
                                    } catch (Exception $e) {
                                        $transaction->rollback();
                                    }
                                }
                            } else {
                                $model->total_price = $stack->price * $model->volume;
                            }

                            Yii::$app->cache->delete($key);
                        } else {
                            $show = false;
                            Yii::$app->session->setFlash('danger', '对不起,重复提交!');
                        }
                    }

        } else {
                $show = false;
                Yii::$app->session->setFlash('danger', '非交易时间. 早上10:00 ~ 12:30. 下午2:00 ~ 4:00');
            }
        } else {
            $show = false;
            Yii::$app->session->setFlash('danger', '对不起,非交易日不能进行交易!');
        }
        return $this->render('sell', [
            'model' => $model,
            'stack' => $stack,
            'show' => $show,
            'memberStack' => $memberStack
        ]);
    }


    public function actionIndex()
    {
        $stacks = Stack::find()->all();

        return $this->render('index', [
            'stacks' => $stacks
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

    public function actionPrices()
    {
        $stacks = Stack::getStackNameOptions();
        $prices = array();

        foreach ($stacks as $id => $s) {
            $prices['price' . $id] = Stack::getPrice($id);
        }

        echo json_encode($prices);
        Yii::$app->response->send();
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
