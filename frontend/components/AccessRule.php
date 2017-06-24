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
        if (!$user->getIsGuest()) {
            $loginKey = Yii::$app->session->get('LOGIN_KEY_' . $user->id);
            $cacheKey = Yii::$app->user->identity->login_key;
        }

        if ($user->getIsGuest()) {
            return parent::matchRole($user);
        } else if ((Yii::$app->user->identity->login_key == $loginKey) && System::loadConfig('enable_memmber_login') && $user->getIdentity() && (Yii::$app->params['country'] == ($user->getIdentity()->country))) {
            return parent::matchRole($user);
        } else {
            Yii::$app->user->logout();

            if ((System::loadConfig('enable_memmber_login')) && $user->getIdentity() && (Yii::$app->params['country'] != ($user->getIdentity()->country))) {
                Yii::$app->session->setFlash('danger', '您不能登录该站点，请与管理员联系');

            } else if ($cacheKey != $loginKey) {
                Yii::$app->session->setFlash('danger', '您已经在其它地方登录, 请与管理员联系');
            }

            return Yii::$app->getResponse()->redirect('/site/login');
        }
    }

}
