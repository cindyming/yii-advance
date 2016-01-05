<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "member_stack".
 *
 * @property integer $id
 * @property integer $member_id
 * @property integer $stack_id
 * @property integer $sell_volume
 * @property integer $lock_volume
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Member $member
 * @property Stack $stack
 */
class MemberStack extends ActiveRecord
{
    public $membername;
    public $stackcode;
    public $stackname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'member_stack';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['member_id', 'stack_id', 'sell_volume', 'lock_volume'], 'required'],
            [['member_id', 'stack_id', 'sell_volume', 'lock_volume'], 'integer'],
            [['created_at', 'updated_at'], 'safe']
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
            'sell_volume' => Yii::t('app', 'Sell Volume'),
            'lock_volume' => Yii::t('app', 'Lock Volume'),
            'created_at' => Yii::t('app', 'Created At'),
            'membername' => Yii::t('app', 'Member Name'),
            'current_price' => Yii::t('app', 'Current Price'),
            'total_price' => Yii::t('app', 'Total Price'),
            'stackname' => Yii::t('app', 'Stack Name'),
            'stackcode' => Yii::t('app', 'Stack Code'),
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStack()
    {
        return $this->hasOne(Stack::className(), ['id' => 'stack_id']);
    }

    public static function getMemberStack($transaction)
    {
        $volume = $transaction->volume;
        $member_id = $transaction->member_id;
        $stack_id = $transaction->stack_id;
        $model = MemberStack::find()->where(['=', 'member_id', $member_id])->andWhere(['=', 'stack_id', $stack_id])->one();

        if ($model && $model->id) {
            if ($transaction->type === 0) {
                $model->lock_volume += $volume;
            } else {
                $model->lock_volume -= $volume;
            }
        } else {
            if ($transaction->type === 0) {
                $model = new MemberStack();
                $data = array(
                    'member_id' => $member_id,
                    'stack_id' => $stack_id,
                    'lock_volume' => $volume,
                    'sell_volume' => 0
                );
                $model->load($data, '');
            }

        }
        return $model;
    }
}
