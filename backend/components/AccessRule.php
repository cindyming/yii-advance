<?php
namespace backend\components;
use backend\models\User;
use Yii;
class AccessRule extends \yii\filters\AccessRule
{
    /**
     * @inheritdoc
     */
    protected function matchRole($user)
    {
        if (count($this->roles) === 0) {
            return true;
        }
        foreach ($this->roles as $role) {
            if (($role === User::SUPPER_ADMIN)) {
                if ((!$user->getIsGuest()) && ($user->identity->role_id === User::SUPPER_ADMIN)) {
                    return true;
                }
            } else if (($role === User::STACK_ADMIN)) {
                if ((!$user->getIsGuest()) && ($user->identity->role_id === User::STACK_ADMIN)) {
                    return true;
                }
            }  else if (($role === User::STACK_TWO_ADMIN)) {
                if ((!$user->getIsGuest()) && ($user->identity->role_id === User::STACK_TWO_ADMIN)) {
                    return true;
                }
            }elseif ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif (!$user->getIsGuest() && $role === $user->identity->role_id) {
                if (($user->identity->role_id != 1)) {
                    Yii::$app->user->logout();
                    Yii::$app->getResponse()->redirect('/site/login');
                }
                return true;
            }
        }
        return false;
    }
}
