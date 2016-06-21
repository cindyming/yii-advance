<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;


/**
 * This is the model class for table "stack_trends".
 *
 * @property integer $id
 * @property integer $stack_id
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Stack $stack
 */
class StackTrends extends ActiveRecord
{
    public $name;
    public $code;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stack_trends';
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
            [[ 'stack_id', 'price'], 'required'],
            [[ 'stack_id'], 'integer'],
            [['price'], 'number'],
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
            'stack_id' => Yii::t('app', 'Stack ID'),
            'price' => Yii::t('app', 'Price'),
            'name' => Yii::t('app', 'Stack Name'),
            'code' => Yii::t('app', 'Stack Code'),
            'created_at' => Yii::t('app', 'Stack Trends Created At'),
            'updated_at' => Yii::t('app', 'Stack Trends Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStack()
    {
        return $this->hasOne(Stack::className(), ['id' => 'stack_id']);
    }

    public function getCode()
    {
        $stacks = Stack::getStackCodeOptions();

        return isset($stacks[$this->stack_id]) ?  $stacks[$this->stack_id] : '';
    }

    public function getName()
    {
        $stacks = Stack::getStackNameOptions();

        return isset($stacks[$this->stack_id]) ?  $stacks[$this->stack_id] : '';
    }
}
