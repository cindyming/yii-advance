<?php
namespace frontend\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\web\IdentityInterface;
use common\models\MemberStack;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 10;
    public $captcha;

    public $password;
    public $password_confirm;
    public $password2_confirm;
    public $password2;
    public $password2_old;
    public $password_old;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'username', 'identity', 'phone', 'title', 'investment', 'bank', 'country', 'cardname', 'cardnumber', 'bankaddress'], 'required'],
            [['status', 'locked', 'role_id', 'investment', 'buy_stack', 'added_by'], 'integer'],
            [['password_old', 'password2_old', 'password_hash', 'password_hash2', 'password', 'password2', 'password_confirm', 'password2_confirm'], 'string', 'min' => 6],
            [['created_at', 'updated_at', 'approved_at'], 'safe'],
            [['stack_fund', 'finance_fund'], 'number'],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password'],
            [['password2_confirm'], 'compare', 'compareAttribute' => 'password2'],
            [['auth_key', 'username', 'access_token', 'nickname', 'password_hash', 'password_hash2', 'identity', 'phone', 'cardname', 'cardnumber', 'email', 'qq'], 'string', 'max' => 250],
            [['title', 'bank','auth_key', 'nickname'], 'string', 'max' => 100],
            [['bankaddress'], 'string', 'max' => 600]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => sstrtolower(str_replace(' ', '', $$username)), 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function validatePassword2($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash2);
    }
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function setPassword2($password)
    {
        $this->password_hash2 = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function isAdmin()
    {
        return false;
    }

    public function isMember()
    {
        return true;
    }

    public function getMemberStack($stack_id)
    {
        return MemberStack::find()->where(['member_id' => Yii::$app->user->identity->id,  'stack_id' => $stack_id])->one();
    }

    public  function canBuyStock()
    {
        return (($this->buy_stack == 1)) ? true : false;
    }
}
