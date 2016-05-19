<?php

namespace api\modules\v1\controllers;

use backend\models\User;
use Yii;
use yii\rest\ActiveController;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use yii\filters\auth\HttpBasicAuth;

/**
 * Country Controller API
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class AccountController extends ActiveController
{
    public $modelClass = 'api\modules\v1\models\Account';

    private $accessRules = [
        'index' => 'getCountriesList',
        'view' => 'getCountryDetails',
        'create' => 'createCountryDetails',
        'update' => 'updateCountryDetails',
        'delete' => 'deleteCountryDetails',
        'add' => 'createCountryDetails'
    ];


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['contentNegotiator']['formats']['text/html'] = Response::FORMAT_JSON; //setting JSON as default reply
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
            'auth' => array($this, 'auth')
        ];

        return $behaviors;
    }

    public function checkAccess($action, $model = null, $params = [])
    {
         if (!isset($this->accessRules[$action]) || !Yii::$app->user->identity->can($this->accessRules[$action])) {
             throw new ForbiddenHttpException;
         }
    }

    public function auth($username, $password) {
        // username, password are mandatory fields
        if(empty($username) || empty($password))
            return null;

        // get user using requested email
        $user = User::findOne([
            'username' => $username,
        ]);

        // if no record matching the requested user
        if(empty($user))
            return null;

        // validate password
        $isPass = $user->validatePassword($password);

        // if password validation fails
        if(!$isPass)
            return null;

        // if user validates (both user_email, user_password are valid)
        return $user;
    }

}