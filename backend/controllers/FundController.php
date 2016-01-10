<?php

namespace backend\controllers;

use backend\components\AccessRule;
use backend\models\User;
use common\models\FundTransaction;
use common\models\InRecord;
use common\models\Member;
use common\models\search\FundTransactionSearch;
use Yii;
use common\models\Fund;
use common\models\search\FundSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * FundController implements the CRUD actions for Fund model.
 */
class FundController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'except' => ['login', 'logout', 'autologin'],
                'ruleConfig' => [
                    'class' => AccessRule::className(),
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'history', 'view', 'create', 'update', 'lock', 'clear', 'settings', 'delete', 'add', 'validateadd'],
                        'roles' => [User::SUPPER_ADMIN]
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

    /**
     * Lists all Fund models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FundSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionHistory()
    {
        $searchModel = new FundTransactionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('history', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Fund model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionValidateadd()
    {
        $model = new FundTransaction();

        if ($model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));

        } else {
            echo json_encode(array());
        }
        Yii::$app->end();
    }

    public function actionAdd()
    {
        $model = new FundTransaction();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                $model->member_id = Member::find()->where(['=', 'username', $model->membername])->one()->id;
                if ($model->save()) {
                    return $this->redirect(['history']);
                }
            }

        }
        return $this->render('add', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Fund model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Fund();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionLock($id)
    {
        $model = $this->findTransaction($id);
        $model->locked = 1;
        $model->save();
        return $this->redirect(['history']);
    }

    public function actionClear($id)
    {
        $model =$this->findTransaction($id);
        $model->cleared = 1;
        $model->cleared_at = date('Y-m-d H:i:s');
        $connection = Yii::$app->db;
        try {
            $member = $model->member;
            $member->finance_fund += $model->investment;
            $inRecord = InRecord::prepareModelForSellStack($model->member_id, ($model->investment),$member->finance_fund, 0);
            $inRecord->note ='基金[' . $model->fund->name . ']清仓';
            $inRecord->type = 5;
            $transaction = $connection->beginTransaction();
            if ($model->save() && $inRecord->save() && $member->save()) {
                $transaction->commit();
                return $this->redirect(['history']);
            } else {
                Yii::error('Fund Clear Failed');
                Yii::error( json_encode($model->getErrors()));
                Yii::error( json_encode($inRecord->getErrors()));
                Yii::error( json_encode($member->getErrors()));
                $transaction->rollback();
            }
        } catch (Exception $e) {

        }
        return $this->redirect(['history']);
    }


    public function actionSettings()
    {
        $model = $this->findModel(1);
        $data = $model->getAttributes();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newData = $model->getAttributes();
            $from = array_diff_assoc($data, $newData);
            $to = array_diff_assoc($newData, $data);
            Yii::$app->session->setFlash('success', '参数修改成功.');
            Yii::$app->systemlog->add('管理员', '修改基金参数', '成功',  ': 从 (' . json_encode($from) . ')改为（' . json_encode($to) . ')');
        }
        return $this->render('settings', [
            'model' => $model,
        ]);

    }

    /**
     * Updates an existing Fund model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Fund model.
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
     * Finds the Fund model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Fund the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Fund::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findTransaction($id)
    {
        if (($model = FundTransaction::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
