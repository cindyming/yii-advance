<?php
namespace frontend\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $captcha;
    public $rememberMe = false;

    private $_user;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
            ['captcha', 'captcha','captchaAction'=>'site/captcha' ],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, '会员编号或密码错误');
            }
        }
    }

    public function attributeLabels()
    {
        return [
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Login Password'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        } else {
            return false;
        }
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByUsername($this->username);
            if ($this->_user) {
                if ($this->_user->role_id != 3) {
                    $this->_user = null;
                    $this->addError('username', '此帐户未被审核，请联系管理员');
                }
            }

            if ($this->_user) {
                if ($this->_user->locked) {
                    $this->_user = null;
                    $this->addError('username', '此帐户已被锁定，请联系管理员');
                }
            }

            if ($this->_user) {
                if (!$this->_user->login_auth) {
                    $this->_user = null;
                    $this->addError('username', '此帐户无法登录委托平台，请联系管理员');
                }
            }

        }

        return $this->_user;
    }
}
