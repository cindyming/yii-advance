<?php

namespace backend\controllers;

use common\models\Cash;
use common\models\InRecord;
use common\models\Member;
use common\models\OutRecord;
use common\models\search\CashSearch;
use backend\models\User;
use yii;
use common\models\search\InRecordSearch;
use common\models\search\OutRecordSearch;
use common\models\search\MemberSearch;

class AccountController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'except' => ['login', 'logout', 'autologin'],
                'ruleConfig' => [
                    'class' => \backend\components\AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['list', 'cashlist', 'inlist', 'outlist', 'reject', 'add', 'approve', 'validateadd'],
                        'roles' => [\backend\models\User::SUPPER_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['list', 'cashlist', 'inlist', 'outlist'],
                        'roles' => [\backend\models\User::STACK_TWO_ADMIN]
                    ],
                ],
            ],
        ];
    }

    public function actionValidateadd()
    {
        if (Yii::$app->request->get('type') == 'out') {
            $model = new OutRecord();
        } else {
            $model = new InRecord();
        }

        Yii::$app->response->format = yii\web\Response::FORMAT_JSON;

        if ($model->load(Yii::$app->request->post())) {
            $member = Member::isExist($model->membername);
            $result = yii\widgets\ActiveForm::validate($model);

            if (!$member) {
                $model->addError('membername', '用户编号不存在,请确认后输入');
            } else {
                if (Yii::$app->request->get('type') == 'out') {
                    if ($model->account_type == 1) {
                        $compareData = $member->finance_fund;
                    } else {
                        $compareData = $member->stack_fund;
                    }
                    if ($model->amount > $compareData) {
                        $model->addError('amount', '账户余额不足,理财账户余额: ' . $member->finance_fund . '. 购股账户余额: '. $member->stack_fund);
                    }
                }
            }
            foreach ($model->getErrors() as $attribute => $errors) {
                $result[yii\helpers\Html::getInputId($model, $attribute)] = $errors;
            }
            echo json_encode($result);
        } else {
            echo json_encode(array());
        }
        Yii::$app->end();
    }

    public function actionAdd()
    {
        if (Yii::$app->request->get('type') == 'out') {
            $model = new OutRecord();
            $model->type = 4;
        } else {
            $model = new InRecord();
            $model->type = 1;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($member = Member::isEnabled($model->membername)) {
                $validate = true;
                if (Yii::$app->request->get('type') == 'out') {
                    if ($model->account_type == 1) {
                        $compareData = $member->finance_fund;
                    } else {
                        $compareData = $member->stack_fund;
                    }
                    if ($model->amount > $compareData) {
                        $validate = false;
                        $model->addError('amount', '账户余额不足,理财账户余额: ' . $member->finance_fund . '. 购股账户余额: '. $member->stack_fund);
                    }
                }

                if ($validate) {
                    if (Yii::$app->request->get('type') == 'out') {
                        if ($model->account_type == 1) {
                            $member->finance_fund -= $model->amount;
                            $model->total = $member->finance_fund;
                        } else {
                            $member->stack_fund -= $model->amount;
                            $model->total = $member->stack_fund;
                        }
                    } else {
                        if ($model->account_type == 1) {
                            $member->finance_fund += $model->amount;
                            $model->total = $member->finance_fund;
                        } else {
                            $member->stack_fund += $model->amount;
                            $model->total = $member->stack_fund;
                        }
                    }

                    $model->fee = 0;
                    $model->member_id = $member->id;
                    if ($member->save() && $model->save())  {
                        if (Yii::$app->request->get('type') == 'out') {
                            return $this->redirect(['outlist']);
                        } else {
                            return $this->redirect(['inlist']);
                        }

                    } else {
                        var_dump($member->getErrors());
                        var_dump($model->getErrors());die;
                    }
                }
            } else {
                $model->addError('membername', '用户编号不存在或者没有通过审核,请确认后输入');
            }
        }
        return $this->render('add', [
            'model' => $model,
        ]);

    }


    public function actionApprove($id)
    {
        $model = $this->findModel($id);

        $connection=Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();

            $model->status = 2;
            $user = Member::findOne($model->member_id);
            $model->total = $user->finance_fund;
            $model->note = '提现成功';
            Yii::$app->session->setFlash('success', '会员(' . $user->username. ')提现申请发放成功');
            $model->save();
            $user->save();
            $transaction->commit();
            $this->redirect(Yii::$app->request->referrer);
            return;
        } catch (Exception $e) {
            $transaction->rollback();//回滚函数
        }


        return $this->redirect(['cashlist', 'id' => $model->id]);

    }

    protected function findModel($id)
    {
        if (($model = Cash::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $connection = Yii::$app->db;
        try {
            $transaction = $connection->beginTransaction();
            $model->status = 3;

            $user = Member::findOne($model->member_id);
            $user->finance_fund += $model->amount;
            $data = array(
                'member_id' => $model->member_id,
                'account_type' => 1,
                'amount' => $model->amount,
                'fee' => 0,
                'total' => $user->finance_fund,
                'type' => 4,
                'note' => '拒绝提现,货币返还.'
            );
            $model->note .= '拒绝提现,货币返还.';
            Yii::$app->session->setFlash('success', '提现申请拒绝成功');
            $revenue = new InRecord();
            $revenue->load($data, '');
            $revenue->save();
            $user->save();
            $model->save();
            $transaction->commit();
            $this->redirect(Yii::$app->request->referrer);
            return;
        } catch (Exception $e) {
            $transaction->rollback();//回滚函数
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        return $this->redirect(['cashlist', 'id' => $model->id]);
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

}
