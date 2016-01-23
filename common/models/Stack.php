<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "stack".
 *
 * @property integer $id
 * @property string $name
 * @property string $code
 * @property integer $status
 * @property string $price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property StackTransaction[] $stackTransactions
 * @property StackTrends[] $stackTrends
 */
class Stack extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stack';
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
            [['name', 'code', 'status', 'price'], 'required'],
            [['status'], 'integer'],
            [['price'], 'number'],
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
            'name' => Yii::t('app', 'Name'),
            'code' => Yii::t('app', 'Code'),
            'status' => Yii::t('app', 'Stack Status'),
            'price' => Yii::t('app', 'Price'),
            'created_at' => Yii::t('app', 'Stack Created At'),
            'updated_at' => Yii::t('app', 'Stack Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStackTransactions()
    {
        return $this->hasMany(StackTransaction::className(), ['stack_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStackTrends()
    {
        return $this->hasMany(StackTrends::className(), ['stack_id' => 'id']);
    }

    public static function getStackOptions()
    {
        $stacks = Stack::findAll(array('status' => 0));
        return ArrayHelper::map($stacks, 'code', 'code');
    }
}
