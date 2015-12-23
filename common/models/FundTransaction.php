<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fund_transaction".
 *
 * @property integer $id
 * @property integer $fund_id
 * @property integer $member_id
 * @property string $investment
 * @property string $revenue
 * @property integer $locked
 * @property integer $cleared
 * @property string $created_at
 * @property string $cleared_at
 *
 * @property Fund $fund
 * @property Member $member
 */
class FundTransaction extends \yii\db\ActiveRecord
{
    public $membername;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fund_transaction';
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
            [['fund_id', 'member_id', 'investment', 'revenue', 'locked', 'cleared'], 'required'],
            [['fund_id', 'member_id', 'locked', 'cleared'], 'integer'],
            [['investment', 'revenue'], 'number'],
            [['created_at', 'cleared_at'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'fund_id' => Yii::t('app', 'Fund ID'),
            'member_id' => Yii::t('app', 'Member ID'),
            'investment' => Yii::t('app', 'Fund Investment'),
            'revenue' => Yii::t('app', 'Revenue'),
            'locked' => Yii::t('app', 'Locked'),
            'cleared' => Yii::t('app', 'Cleared'),
            'created_at' => Yii::t('app', 'Created At'),
            'cleared_at' => Yii::t('app', 'Cleared At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFund()
    {
        return $this->hasOne(Fund::className(), ['id' => 'fund_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMember()
    {
        return $this->hasOne(Member::className(), ['id' => 'member_id']);
    }
}
