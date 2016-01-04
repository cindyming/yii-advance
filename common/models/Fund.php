<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "fund".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property FundTransaction[] $fundTransactions
 */
class Fund extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'fund';
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
            [['name', 'code', 'status'], 'required'],
            [['status'], 'integer'],
            [['daily', 'monthly', 'excepted'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['name'], 'string', 'max' => 250],
            [['code'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Fund Name'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Status'),
            'membername' => Yii::t('app', 'Member Name'),
            'daily' => Yii::t('app', 'Daily Revenue'),
            'monthly' => Yii::t('app', 'Month Revenue'),
            'excepted' => Yii::t('app', 'Excepted Revenue'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFundTransactions()
    {
        return $this->hasMany(FundTransaction::className(), ['fund_id' => 'id']);
    }

    public static function getFund()
    {
        return self::findOne(1);
    }
}
