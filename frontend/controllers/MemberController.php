<?php

namespace frontend\controllers;

use Yii;
use common\models\Member;
use common\models\search\MemberSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
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
     * Lists all Member models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MemberSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChangepassword()
    {
        $model = Yii::$app->user->identity;
        $success = false;

        $data = Yii::$app->request->post('User');
        if (count($data)) {
            $message = '';
            if (isset($data['password'])) {
                if ($model->validatePassword($data['password_old'])) {
                    $model->setPassword($data['password']);
                    if ($model->save()) {
                        $success = true;
                        $message = '一级密码修改成功';
                    }
                } else {
                    $model->addError('password_old', '原一级密码不正确');
                }
            } else if (isset($data['password2'])){
                if ($model->validatePassword2($data['password2_old'])) {
                    $model->setPassword2($data['password2']);
                    if ($model->save()) {
                        $message = '二级密码修改成功';
                        $success = true;
                    }
                } else {
                    $model->addError('password2_old', '原二级密码不正确');
                }
            }
        }

        if ($success) {
            Yii::$app->session->setFlash('success', $message);
        }
            return $this->render('changepassword', [
                'model' => $model,
            ]);
    }

    /**
     * Displays a single Member model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionValidate()
    {
        $model = new Member();
        if($model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));
        } else {var_dump('dd');
            echo json_encode(array());
        }
        Yii::$app->end();
    }

    /**
     * Creates a new Member model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Member();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionAutologin($id)
    {
        $member = $this->findModel($id);
        Yii::$app->user->login($member);

        return $this->redirect(['news/index']);
    }

    /**
     * Deletes an existing Member model.
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
     * Finds the Member model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Member the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Member::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
