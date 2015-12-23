<?php

namespace backend\controllers;

use Yii;
use common\models\Backup;
use common\models\search\BackupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BackupController implements the CRUD actions for Backup model.
 */
class BackupController extends Controller
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
     * Lists all Backup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new BackupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate()
    {
        $filename = 'backup_'.date('d_m_Y_h_i_s', time()) . '.sql.gz';
        system('/alidata/server/mysql/bin/mysqldump -uroot  -p' .Yii::$app->db->password. ' mgjiayuan --add-drop-table | gzip  > /home/backup/' . $filename, $output);
        $backup = new Backup();
        $backup->filename = $filename;
        if ($backup->save()) {
            Yii::$app->session->setFlash('success', '数据库备份成功');
        } else {
            Yii::$app->session->setFlash('success', '数据库备份失败，请稍后再试');
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Backup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Backup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Backup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
