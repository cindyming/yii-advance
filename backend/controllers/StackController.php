<?php

namespace backend\controllers;

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
use yii\caching\MemCache;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use backend\models\User;
use yii\widgets\ActiveForm;
use yii\web\Response;


/**
 * StackController implements the CRUD actions for Stack model.
 */
class StackController extends Controller
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
                        'actions' => ['index', 'trends', 'transactions', 'unlock', 'view', 'create', 'validatebuy', 'buy', 'update', 'delete', 'fund'],
                        'roles' => [User::SUPPER_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'trends', 'transactions', 'unlock','update'],
                        'roles' => [User::STACK_ADMIN]
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
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
                    Yii::$app->session->setFlash('success', '交易解锁成功');
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '交易解锁失败, 请稍后再试或联系管理员');
                }
            } else {
                $stackTransaction->status = 1;
                $memberStack = $stackTransaction->getMemberStack()->one();
                $memberStack->lock_volume -= $stackTransaction->volume;
                $member = $stackTransaction->getMember()->one();
                $fee = round($stackTransaction->total_price * System::loadConfig('sell_fee_rate'), 2);
                $member->finance_fund += ($stackTransaction->total_price - $fee);
                $stackOutRecord = InRecord::prepareModelForSellStack($stackTransaction->member_id, ($stackTransaction->total_price - $fee), $member->finance_fund, $fee);
                $stackOutRecord->note = '出售[' . $stackTransaction->stack->code . ']' . $stackTransaction->volume . '股';
                if ($memberStack->save() && $stackTransaction->save() && $member->save() && $stackOutRecord->save()) {
                    $transaction->commit();
                    Yii::$app->session->setFlash('success', '交易解锁成功');
                } else {
                    $transaction->rollBack();
                    Yii::$app->session->setFlash('danger', '交易解锁失败, 请稍后再试或联系管理员');
                }
            }
        } catch (Exception $e) {
            $transaction->rollBack();
        }
        $this->redirect(Yii::$app->request->referrer);
        return;
    }

    /**
     * Lists all Stack models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StackSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (isset($_POST['hasEditable'])) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model = Stack::findOne($_POST['editableKey']);
            if ($model && $model->id) {
                $price = $_POST['Stack'][$_POST['editableIndex']]['price'];
                $model->price = $price;
                $stackTrends = new StackTrends();
                $stackTrends->load(array(
                    'stack_id' => $model->id,
                    'price' => $model->price,
                ), '');
                $stackTrends->save();
                $model->save();

                // return JSON encoded output in the below format
                return ['output' => $price, 'message' => ''];

                // alternatively you can return a validation error
                // return ['output'=>'', 'message'=>'Validation error'];
            } // else if nothing to do always return an empty JSON encoded output
            else {
                return ['output' => '', 'message' => 'Update Failed'];
            }
        }

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

    /**
     * Displays a single Stack model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Stack model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Stack();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionValidatebuy()
    {
        $model = new StackTransaction();
        if($model->load(Yii::$app->request->post()))
        {
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

        if ($model->load(Yii::$app->request->post())) {
            $validate = true;;
            if ($validate) {
                $model->setStackId();
                $model->setMemberId();
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
                } else if ($member->stack_fund > 0){
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
                    }elseif ($stackOutRecord && $model->save() && $memberStack->save() && $member->save() && $stackOutRecord->save()) {
                        $success = true;
                    }
                    if ($success) {
                        $transaction->commit();
                        return $this->redirect(['transactions']);
                    } else {
                        Yii::error('Stack Buy Failed');
                        Yii::error( json_encode($model->getErrors()));
                        Yii::error( json_encode($memberStack->getErrors()));
                        Yii::error( json_encode($member->getErrors()));
                        if ($stackOutRecord) {
                            Yii::error( json_encode($stackOutRecord->getErrors()));
                        }
                        if ($financeOutRecord) {
                            Yii::error( json_encode($financeOutRecord->getErrors()));
                        }
                        $transaction->rollback();
                    }

                }  catch (Exception $e) {
                }
            } else {
                $model->validate();
            }

        }
        return $this->render('buy', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Stack model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldModel = $this->findModel($id);;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($oldModel->price != $model->price) {
                $stackTrends = new StackTrends();
                $stackTrends->load(array(
                    'stack_id' => $model->id,
                    'price' => $model->price,
                ), '');
                $stackTrends->save();
            }
            Yii::$app->session->setFlash('success', '信息修改成功');
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Stack model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Stack model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Stack the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Stack::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
