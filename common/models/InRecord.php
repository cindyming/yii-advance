<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "in_record".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $account_type
 * @property string $amount
 * @property string $fee
 * @property string $total
 * @property string $note
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 */
class InRecord extends \yii\db\ActiveRecord
{
    public $membername;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'in_record';
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
                    if ($this->member_id) {
                        return $this->member_id;
                    } else {
                        return Yii::$app->user->id;
                    }

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
            [['member_id', 'account_type', 'amount', 'total'], 'required'],
            [['member_id', 'account_type', 'type'], 'integer'],
            [['amount', 'fee', 'total'], 'number'],
            [['created_at', 'updated_at', 'membername'], 'safe'],
            [['note'], 'string', 'max' => 250]
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
            'account_type' => Yii::t('app', 'Account Type'),
            'amount' => Yii::t('app', 'Amount'),
            'fee' => Yii::t('app', 'Fee'),
            'total' => Yii::t('app', 'Total'),
            'type' => Yii::t('app', 'In Type'),
            'note' => Yii::t('app', 'Note'),
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

    public static function prepareModelForSellStack($member_id, $amount, $total, $fee)
    {
        $data = array(
            'member_id' => $member_id,
            'type' => 2,
            'fee' => $fee,
            'amount' => $amount,
            'total' => $total,
            'account_type' => 1,
            'note' => '出售股票'
        );

        $model = new InRecord();
        $model->load($data, '');
        return $model;
    }
}
