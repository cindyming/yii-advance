<?php
namespace frontend\components;
use common\models\System;
use Yii;

class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (System::loadConfig('enable_memmber_login') && $user->getIdentity() && (Yii::$app->params['country'] == ($user->getIdentity()->country))) {
            return parent::matchRole($user);
        } else {
            Yii::$app->user->logout();
            if ((!System::loadConfig('enable_memmber_login')) && $user->getIdentity() && (Yii::$app->params['country'] != ($user->getIdentity()->country))) {
                Yii::$app->session->setFlash('danger', '您不能登录该站点，请与管理员联系');
            }
            Yii::$app->getResponse()->redirect('/site/login');
        }
    }

}
