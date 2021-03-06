<?php

namespace backend\controllers;

use common\models\Log;
use backend\models\User;
use backend\models\Backup;
use Yii;
use common\models\System;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\filters\AccessControl;
use backend\components\AccessRule;
use yii\filters\VerbFilter;

/**
 * SystemController implements the CRUD actions for System model.
 */
class SystemController extends Controller
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
                        'actions' => ['password', 'log', 'index', 'backup', 'backupindex'],
                        'roles' => [User::SUPPER_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['password'],
                        'roles' => [User::STACK_ADMIN]
                    ],
                    [
                        'allow' => true,
                        'actions' => ['password'],
                        'roles' => [User::STACK_TWO_ADMIN]
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionPassword()
    {
        $model = User::findOne(Yii::$app->user->id);

        $data = Yii::$app->request->post('User');
        if (count($data)) {
            if (isset($data['password'])) {
                if ($model->validateName($data['username'])){
                    if ($model->validatePassword($data['password_old']) && $model->validateName($data['username_old'])) {
                        $model->setAttributes($data);
                        $model->setPassword($model->password);
                        if ($model->save()) {
                            Yii::$app->session->setFlash('success', '密码修改成功');
                        } else {
                            Yii::$app->session->setFlash('danger', '密码修改失败');
                        }
                    } else {
                        Yii::$app->session->setFlash('danger',  '原密码有误, 请输入正确的原密码');
                    }
               } else {
                    Yii::$app->session->setFlash('danger',  '用户名已存在请重试');
                }
            }
        }
        $result['model'] = $model;

        return $this->render('password', $result);
    }

    /**
     * Displays a single System model.
     * @param integer $id
     * @return mixed
     */
    public function actionLog()
    {

        $dataProvider = new ActiveDataProvider([
            'query' => Log::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('log', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all System models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new System();

        $postData = Yii::$app->request->post('System');
        if (count($postData)) {
            foreach ($postData as $key => $da) {
                if ($key == 'lowest_cash_amount') {
                    $da = $da * 100;
                }
                $system = System::findOne(['name' => $key]);
                if ($system && $system->id) {
                    $oldValue = $system->value;
                    $system->value = $da;
                    if ($system->save() && ($da != $oldValue)) {
                        Yii::$app->systemlog->add('管理员', '修改系统参数', '成功', $key . ': 从 (' . $oldValue . ')改为（' . $da . ')');
                    } else if (($da != $oldValue)) {
                        Yii::$app->systemlog->add('管理员', '修改系统参数', '失败', $key . ': 从 (' . $oldValue . ')改为（' . $da . ')');
                    }
                } else {
                    $system = new System();
                    $system->name = $key;
                    $system->value = $da;
                    $system->save();
                }
                Yii::$app->session->setFlash('success', '保存成功.');
            }
            Yii::$app->cache->set('SYSTEM_CONFIG', null);
        }

        $data = System::loadConfig();
        if ($data && count($data)) {
            foreach ($data as $key => $da) {
                $model->$key = $da;
            }
        }

        return $this->render('index', [
            'model' => $model
        ]);
    }

    public function actionBackup()
    {
        $filename = 'backup_'.date('d_m_Y_h_i_s', time()) . '.sql.gz';
        system('/alidata/server/mysql/bin/mysqldump -uroot  -p' .Yii::$app->db->password. ' stack --add-drop-table | gzip  > /home/backup/' . $filename, $output);
        $backup = new Backup();
        $backup->filename = $filename;
        $backup->save();
        Yii::$app->getSession()->set('message', '数据库备份成功.');
        $this->redirect(array('/system/backupindex'));
    }

    public function actionBackupindex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Backup::find()->orderBy(['id' => SORT_DESC]),
            'pagination' => [
                'pageSize' => 20
            ]
        ]);

        return $this->render('backup', [
            'dataProvider' => $dataProvider,
        ]);
    }

}
