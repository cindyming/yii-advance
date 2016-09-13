<?php

namespace backend\controllers;

use common\models\OutRecord;
use common\models\search\MemberStackSearch;
use common\models\System;
use Yii;
use common\models\Member;
use common\models\search\MemberSearch;
use yii\base\Exception;
use yii\data\SqlDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\widgets\ActiveForm;
use backend\components\AccessRule;
use backend\models\User;

/**
 * MemberController implements the CRUD actions for Member model.
 */
class MemberController extends Controller
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
                        'actions' => ['index', 'export', 'approvedindex', 'removeall', 'inactivelist', 'unapprovedindex', 'approve', 'resetpassword', 'reject', 'validate', 'create', 'update','view', 'delete', 'adelete'],
                        'roles' => [User::SUPPER_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'export', 'create','out', 'validate', 'approvedindex'],
                        'roles' => [User::STACK_ADMIN],
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

    /**
     * Lists all Member models.
     * @return mixed
     */
    public function actionApprovedindex()
    {
        $searchModel = new MemberSearch();
        $data = Yii::$app->request->queryParams;
        $data['MemberSearch']['role_id'] = 3;
        $dataProvider = $searchModel->search($data);

        return $this->render('approvedindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all Member models.
     * @return mixed
     */
    public function actionUnapprovedindex()
    {
        $searchModel = new MemberSearch();
        $data = Yii::$app->request->queryParams;
        $data['MemberSearch']['role_id'] = 2;
        $dataProvider = $searchModel->search($data);

        return $this->render('unapprovedindex', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApprove($id)
    {
        $model = $this->findModel($id);
        $data = array('Member' => array('approved_at' => date('Y-m-d h:i:s', time()), 'role_id'=> 3));
        $model->load($data);
        $model->finance_fund -= System::loadConfig('annual_fee');
        $outRecord = OutRecord::prepareYearlyFeeRecord($model->id, $model->finance_fund);

        $connection = Yii::$app->db;
        try{
            $transaction = $connection->beginTransaction();
            if ($model->save() && $outRecord->save()) {
                $transaction->commit();
                Yii::$app->session->setFlash('success', '会员(' .$model->username. ')审核成功');
                return $this->redirect(['approvedindex']);
            } else {
                $transaction->rollBack();
                Yii::$app->session->setFlash('danger', '会员(' .$model->username. ')审核失败, 请稍后再试或联系管理员');
                return $this->redirect(['unapprovedindex']);
            }
        } catch (Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('danger', '会员(' .$model->username. ')审核失败, 请稍后再试或联系管理员');
            return $this->redirect(['unapprovedindex']);
        }
    }

    public function actionInactivelist() {
        $count = Yii::$app->db->createCommand('
                SELECT COUNT(*) FROM member LEFT JOIN
                (select member_id, sum(sell_volume) as sell_total, sum(lock_volume) as lock_total from member_stack group by member_id) stack_member
                on stack_member.member_id=member.id where (sell_total+lock_total) < 100 and role_id=3  and (finance_fund+stack_fund) < 100
            ', [])->queryScalar();

        $dataProvider = new SqlDataProvider([
            'sql' => 'select *, (sell_total+lock_total) as total, finance_fund+stack_fund as itotal FROM member LEFT JOIN
                (select member_id, sum(sell_volume) as sell_total, sum(lock_volume) as lock_total from member_stack group by member_id) stack_member
                on stack_member.member_id=member.id where (sell_total+lock_total) < 100 and role_id=3 and (finance_fund+stack_fund) < 100 order by itotal ASC, total ASC',
            'totalCount' => (int)$count,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);

        return $this->render('inactive', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionResetpassword($id)
    {
        $model = $this->findModel($id);

        if ($model->resetPasword()) {
            Yii::$app->session->setFlash('success', '会员('. $model->username . ')密码重置成功');
        } else {
            Yii::$app->session->setFlash('danger', '会员('. $model->username . ')密码重置失败, 请稍后再试');
        }

        return $this->redirect(['approvedindex']);
    }

    public function actionReject($id)
    {
        $model = $this->findModel($id);
        $data = array('Member' => array('role_id'=> 4));

        if ($model->load($data) && $model->save()) {
            Yii::$app->getSession()->set('message', '会员('. $model->username . ')拒绝成功');
        } else {
            Yii::$app->session->setFlash('danger', '会员(' .$model->username. ')拒绝失败, 请稍后再试或联系管理员');
        }

        return $this->redirect(['unapprovedindex']);
    }

    /**
     * Creates a new Member model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionValidate()
    {
        $model = new Member();
        if($model->load(Yii::$app->request->post()))
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            echo json_encode(ActiveForm::validate($model));

        } else {
            echo json_encode(array());
        }
        Yii::$app->end();
    }

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

    /**
     * Updates an existing Member model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['approvedindex']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
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
        Yii::$app->session->setFlash('success', '会员删除成功');

        return $this->redirect(['unapprovedindex']);
    }

    public function actionRemoveall($id)
    {
        $ids = explode('-', $id);

        foreach ($ids as $id) {
            $model = Member::findOne($id);
            if ($model) {
                $model->role_id = 4;
                $model->save();
            }

        }
        Yii::$app->session->setFlash('success', '会员删除成功');

        return $this->redirect(['inactivelist']);
    }

    public function actionAdelete($id)
    {
        $model = $this->findModel($id);
        $model->role_id = 4;
        $model->save();
        Yii::$app->session->setFlash('success', '会员删除成功');

        return $this->redirect(['approvedindex']);
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

    public function actionExport()
    {
        $searchModel = new MemberSearch();
        $data = Yii::$app->request->queryParams;
        if (Yii::$app->request->get('week', 0)) {
            $data['MemberSearch']['approved_at'] = date('Y-m-d', strtotime('-7 days')) . ' - ' .date('Y-m-d', time());
        } else if ((!isset($data["MemberSearch"])) && (!isset($data["MemberSearch"]['approved_at']))) {
            $data['MemberSearch']['approved_at'] = date('Y-m-d', strtotime('-7 days')) . ' - ' .date('Y-m-d', time());
        }
        $searchModel->export($data);
        return $this->redirect(['/assets/member.xls']);
    }
}
