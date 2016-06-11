<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "health".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $stack_id
 * @property string $created_at
 * @property integer $finish_buy
 * @property integer $finish_sell
 * @property integer $lock_sell
 * @property integer $lock_buy
 * @property integer $exchange_total
 * @property integer $lock_total
 * @property string $updated_at
 */
class Health extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'health';
    }

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
            [['member_id', 'stack_id', 'finish_buy', 'finish_sell', 'lock_sell', 'lock_buy', 'exchange_total', 'lock_total'], 'required'],
            [['member_id', 'stack_id', 'finish_buy', 'finish_sell', 'lock_sell', 'lock_buy', 'exchange_total', 'lock_total'], 'integer'],
            [['created_at', 'updated_at', 'note'], 'safe']
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
            'stack_id' => Yii::t('app', 'Stack ID'),
            'created_at' => Yii::t('app', 'Created At'),
            'finish_buy' => Yii::t('app', 'Finish Buy'),
            'finish_sell' => Yii::t('app', 'Finish Sell'),
            'lock_sell' => Yii::t('app', 'Lock Sell'),
            'lock_buy' => Yii::t('app', 'Lock Buy'),
            'exchange_total' => Yii::t('app', 'Exchange Total'),
            'lock_total' => Yii::t('app', 'Lock Total'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }
}
