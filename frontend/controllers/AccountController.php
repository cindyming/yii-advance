<?php

namespace frontend\controllers;

use common\models\Cash;
use common\models\System;
use yii\filters\AccessControl;
use common\models\Member;
use common\models\OutRecord;
use common\models\search\CashSearch;
use yii;
use common\models\search\InRecordSearch;
use common\models\search\OutRecordSearch;
use common\models\search\MemberSearch;
use common\models\search\FundTransactionSearch;

class AccountController extends \yii\web\Controller
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
                        'actions' => ['list', 'cashlist','charge', 'inlist', 'outlist', 'fund'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionList()
    {
        $searchModel = new MemberSearch();
        $data = Yii::$app->request->queryParams;
        $data['MemberSearch']['role_id'] = 3;
        $dataProvider = $searchModel->search($data);

        return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCashlist()
    {
        $searchModel = new CashSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('cashlist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCharge()
    {
        $model = new Cash();

        $data = Yii::$app->request->post();
        if ($model->load(Yii::$app->request->post()) && isset($data['Cash']) && isset($data['Cash']['password2'])) {
            $validate = true;
            if (!Yii::$app->user->identity->validatePassword2($data['Cash']['password2'])) {
                $validate = false;
                $model->addError('password2', '第二密码错误,请确认后重新输入');
            }
            if ($model->amount > Yii::$app->user->identity->finance_fund) {
                $validate = false;
                $model->addError('amount', '账户余额不足, 理财账户:' . Yii::$app->user->identity->finance_fund);
            }
            if ($validate) {
                $member = Yii::$app->user->identity;
                $member->finance_fund -= $model->amount;
                $model->fee = $model->amount * System::loadConfig('cash_factorage');
                $model->amount = $model->amount - $model->fee;
                $member->save();
                $model->save();
                $outRecord = new OutRecord();
                $data = array(
                    'member_id' => Yii::$app->user->identity->id,
                    'account_type' => 1,
                    'amount' => $model->amount,
                    'fee' => 0,
                    'total' => $member->finance_fund,
                    'type' => 1,
                    'note' => '会员提现'
                );
                $outRecord->load($data, '');
                $outRecord->save();
                Yii::$app->session->setFlash('success', '提现申请提交成功');
                $this->redirect(['cashlist']);
            }
        }

        return $this->render('charge', [
            'model' => $model,
        ]);
    }

    public function actionInlist()
    {
        $searchModel = new InRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('inlist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionOutlist()
    {
        $searchModel = new OutRecordSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('outlist', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFund()
    {
        $searchModel = new FundTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('fund', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}
