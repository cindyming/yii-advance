<?php

namespace backend\controllers;

use Yii;
use common\models\StackAuthorize;
use common\models\search\StackAuthorizeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\User;
use yii\filters\AccessControl;

/**
 * AuthorizeController implements the CRUD actions for StackAuthorize model.
 */
class AuthorizeController extends Controller
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
                        'actions' => ['index', 'create','view', 'update', 'delete', 'sell'],
                        'allow' => true,
                        'roles' => ['@'],
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
     * Lists all StackAuthorize models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StackAuthorizeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single StackAuthorize model.
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
     * Creates a new StackAuthorize model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StackAuthorize();

        if ($model->load(Yii::$app->request->post())) {
            $model->setStackId();
            $model->type = 1;
            $model->setMemberId();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '委托提交成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new StackAuthorize model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionSell()
    {
        $model = new StackAuthorize();

        if ($model->load(Yii::$app->request->post())) {
            $model->setStackId();
            $model->type = 2;
            $model->setMemberId();
            $model->account_type = 0;
            if ($model->save()) {
                Yii::$app->session->setFlash('success', '委托提交成功');
                return $this->redirect(['index']);
            }
        }
        return $this->render('sell', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing StackAuthorize model.
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
     * Deletes an existing StackAuthorize model.
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
     * Finds the StackAuthorize model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StackAuthorize the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StackAuthorize::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
