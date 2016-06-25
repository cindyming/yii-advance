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
        if (System::loadConfig('enable_memmber_login') && (Yii::$app->params['country'] == ($user->country))) {
            return parent::matchRole($user);
        } else {
            Yii::$app->user->logout();
            Yii::$app->getResponse()->redirect('/site/login');
        }
    }

}
