<?php

namespace frontend\controllers;

use common\models\Stack;
use Yii;
use common\models\StackAuthorize;
use common\models\search\StackAuthorizeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use frontend\models\User;
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
        $stack = Stack::findOne(Yii::$app->request->get('id'));
        $stack_code = '';

        if ($model->load(Yii::$app->request->post())) {
            $model->setStackId();
            $model->type = 0;
            $model->member_id = Yii::$app->user->identity->id;
            $totalPrice = $model->volume * $model->price;
            if ((($model->account_type == 1) && Yii::$app->user->identity->finance_fund < $totalPrice) ||
                (($model->account_type == 2) && Yii::$app->user->identity->stack_fund < $totalPrice)) {
                $model->addError('volume', '账户余额不足. 理财基金:' . Yii::$app->user->identity->finance_fund . '. 购股账户:'. Yii::$app->user->identity->stack_fund);
            } else if ($model->save()) {
                Yii::$app->session->setFlash('success', '委托提交成功');
                return $this->redirect(['index']);
            }
        } else {
            $stack_code = $stack->code;
        }

        return $this->render('create', [
            'model' => $model,
            'stack_code' => $stack_code
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
        $stack = Stack::findOne(Yii::$app->request->get('id'));
        $stack_code = '';

        if ($model->load(Yii::$app->request->post())) {
            $model->setStackId();
            $model->type = 1;
            $model->account_type = 0;
            $model->member_id = Yii::$app->user->identity->id;
            $memberStack = Yii::$app->user->identity->getMemberStack($model->stack_id);
            if ($model->checkSellVolume($memberStack, $model->volume) && $model->save()) {
                Yii::$app->session->setFlash('success', '委托提交成功');
                return $this->redirect(['index']);
            }
        } else {
            $stack_code = $stack->code;
        }

        return $this->render('sell', [
            'model' => $model,
            'stack_code' => $stack_code
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
        $model = $this->findModel($id);
        $model->status = 5;
        $model->save();

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
