<?php

namespace backend\controllers;

use Yii;
use common\models\Date;
use common\models\search\DateSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DateController implements the CRUD actions for Date model.
 */
class DateController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Date models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new Date();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model' => $model
        ]);
    }

    /**
     * Displays a single Date model.
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
     * Creates a new Date model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Date();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->start_date < $model->end_date) {
                $realDate = $model->start_date;
                do {
                    $date = Date::find()->where(['=', 'date', $realDate])->one();
                    if (!$date) {
                        $date = new Date();
                        $date->date = $realDate;
                        $date->save();
                    }
                    $realDate = date('Y-m-d',strtotime($realDate . ' +1 day'));

                } while($realDate <= $model->end_date);
            } else {
                $model->addError('end_date', 'End Date Must bigger than Start Date');
            }

        }

        return $this->redirect(['index']);
    }

    /**
     * Updates an existing Date model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $model->status = $model->status ? 0 : 1;
        $model->save();
        Yii::$app->session->setFlash('success', $model->date . ' - 修改成功');
        return $this->redirect(['index']);
    }

    /**
     * Finds the Date model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Date the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Date::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
