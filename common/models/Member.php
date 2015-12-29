<?php

namespace common\models;

use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\db\ActiveRecord;
use common\models\MemberStack;

/**
 * This is the model class for table "member".
 *
 * @property integer $id
 * @property string $auth_key
 * @property string $username
 * @property integer $status
 * @property integer $locked
 * @property string $access_token
 * @property integer $role_id
 * @property string $nickname
 * @property string $password_hash
 * @property string $password_hash2
 * @property string $identity
 * @property string $phone
 * @property string $title
 * @property string $investment
 * @property string $bank
 * @property string $cardname
 * @property string $cardnumber
 * @property string $bankaddress
 * @property string $email
 * @property string $qq
 * @property string $created_at
 * @property string $updated_at
 * @property string $approved_at
 * @property integer $buy_stack
 * @property integer $added_by
 * @property string $stack_fund
 * @property string $finance_fund
 */
class Member extends ActiveRecord
{
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
        return 'member';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'auth_key',
                ],
                'value' => function ($event) {
                    return sha1(rand());
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'finance_fund',
                ],
                'value' => function ($event) {
                    return $this->investment;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'access_token',
                ],
                'value' => function ($event) {
                    return sha1(rand());
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'password_hash',
                ],
                'value' => function ($event) {
                    return Yii::$app->security->generatePasswordHash($this->password);
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'password_hash2',
                ],
                'value' => function ($event) {
                    return Yii::$app->security->generatePasswordHash($this->password2);
                },
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[ 'username', 'identity', 'phone', 'title', 'investment', 'bank', 'cardname', 'cardnumber', 'bankaddress'], 'required'],
            [['status', 'locked', 'role_id', 'investment', 'buy_stack', 'added_by'], 'integer'],
            [['password_old', 'password2_old', 'password_hash', 'password_hash2', 'password', 'password2', 'password_confirm', 'password2_confirm'], 'string', 'min' => 6],
            [['created_at', 'updated_at', 'approved_at'], 'safe'],
            [['stack_fund', 'finance_fund'], 'number'],
            [['username'], 'checkUsername'],
            [['password_confirm'], 'compare', 'compareAttribute' => 'password'],
            [['password2_confirm'], 'compare', 'compareAttribute' => 'password2'],
            [['auth_key', 'username', 'access_token', 'nickname', 'password_hash', 'password_hash2', 'identity', 'phone', 'cardname', 'cardnumber', 'email', 'qq'], 'string', 'max' => 250],
            [['title', 'bank','auth_key', 'nickname'], 'string', 'max' => 100],
            [['bankaddress'], 'string', 'max' => 600]
        ];
    }

    public function getAddedByMember()
    {
        if ($this->added_by) {
            return $this->hasOne(Member::className(), ['id' => 'added_by'])->from(Member::tableName().' us');
        } else {
            return null;
        }
    }

    public function getStatus()
    {
        return ($this->role_id == 3) ? '正式' : ( ($this->role_id == 4) ? '拒绝' : '待审核');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'auth_key' => Yii::t('app', 'Auth Key'),
            'username' => Yii::t('app', 'Username'),
            'password' => Yii::t('app', 'Password'),
            'password2' => Yii::t('app', 'Password2'),
            'password_confirm' => Yii::t('app', 'Password Confirm'),
            'password2_confirm' => Yii::t('app', 'Password2 Confirm'),
            'status' => Yii::t('app', 'Status'),
            'locked' => Yii::t('app', 'Locked'),
            'access_token' => Yii::t('app', 'Access Token'),
            'role_id' => Yii::t('app', 'Role ID'),
            'nickname' => Yii::t('app', 'Nickname'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'password_hash2' => Yii::t('app', 'Password Hash2'),
            'identity' => Yii::t('app', 'Identity'),
            'phone' => Yii::t('app', 'Phone'),
            'title' => Yii::t('app', 'Member Title'),
            'investment' => Yii::t('app', 'Investment'),
            'bank' => Yii::t('app', 'Bank'),
            'cardname' => Yii::t('app', 'Cardname'),
            'cardnumber' => Yii::t('app', 'Cardnumber'),
            'bankaddress' => Yii::t('app', 'Bankaddress'),
            'email' => Yii::t('app', 'Email'),
            'qq' => Yii::t('app', 'Qq'),
            'created_at' => Yii::t('app', 'Registered At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'approved_at' => Yii::t('app', 'Approved At'),
            'buy_stack' => Yii::t('app', 'Buy Stack'),
            'added_by' => Yii::t('app', 'Added By'),
            'stack_fund' => Yii::t('app', 'Stack Fund'),
            'finance_fund' => Yii::t('app', 'Finance Fund'),
        ];
    }

    public function checkUsername($attribute, $param)
    {
        $existUser = Member::find()->where(['=', 'username', $this->username])->one();
        if($existUser && ($existUser->id != $this->id)){
            $this->addError($attribute, '该用户名已存在，请重新输入一个!');
        }
    }

    public function resetPasword()
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash('123456');
        $this->password_hash2 = Yii::$app->security->generatePasswordHash('123456');
        return $this->save();
    }

    public static function isExist($usename)
    {
        $existUser = Member::find()->where(['=', 'username', $usename])->one();;
        return $existUser;
    }
}
