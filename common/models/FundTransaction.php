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
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'fund_id',
                ],
                'value' => function ($event) {
                    return 1;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'revenue',
                ],
                'value' => function ($event) {
                    return 0;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'locked',
                ],
                'value' => function ($event) {
                    return 0;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'cleared',
                ],
                'value' => function ($event) {
                    return 0;
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
            [[ 'investment'], 'required'],
            [['fund_id', 'member_id', 'locked', 'cleared'], 'integer'],
            [['investment', 'revenue'], 'number'],
            [['membername'], 'checkUsername'],
            [['created_at', 'cleared_at', 'fund_id', 'revenue', 'locked', 'cleared'], 'safe']
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
            'membername' => Yii::t('app', 'Member Name'),
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

    public function checkUsername($attribute, $param)
    {
        $existUser = Member::find()->where(['=', 'username', $this->membername])->one();
        if(!$existUser){
            $this->addError($attribute, '该用户不存在!');
        }
    }
}
