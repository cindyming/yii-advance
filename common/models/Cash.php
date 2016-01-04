<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "cash".
 *
 * @property integer $id
 * @property integer $member_id
 * @property string $bank
 * @property string $cardname
 * @property string $backaddress
 * @property string $cardnumber
 * @property string $amount
 * @property string $fee
 * @property string $real_amount
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 */
class Cash extends \yii\db\ActiveRecord
{
    public $membername;
    public $password2;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cash';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'member_id',
                ],
                'value' => function ($event) {
                    return Yii::$app->user->identity->id;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'real_amount',
                ],
                'value' => function ($event) {
                    return $this->amount - $this->fee;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                    return 1;
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
            [[ 'bank', 'cardname', 'backaddress', 'cardnumber', 'amount', 'fee'], 'required'],
            [['member_id', 'status'], 'integer'],
            [['amount', 'fee', 'real_amount'], 'number'],
            [['created_at', 'updated_at', 'membername', 'password2'], 'safe'],
            [['bank'], 'string', 'max' => 100],
            [['cardname', 'backaddress', 'cardnumber'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'member_id' => Yii::t('app', 'Member ID'),
            'bank' => Yii::t('app', 'Bank'),
            'cardname' => Yii::t('app', 'Cardname'),
            'backaddress' => Yii::t('app', 'Backaddress'),
            'cardnumber' => Yii::t('app', 'Cardnumber'),
            'membername' => Yii::t('app', 'Member Name'),
            'amount' => Yii::t('app', 'Amount'),
            'fee' => Yii::t('app', 'Fee'),
            'real_amount' => Yii::t('app', 'Real Amount'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }
}
