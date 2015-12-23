<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stack_transaction".
 *
 * @property integer $id
 * @property integer $stack_id
 * @property integer $member_id
 * @property integer $volume
 * @property string $in_price
 * @property string $out_price
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Stack $stack
 * @property Member $member
 */
class StackTransaction extends ActiveRecord
{
    public $stackname;
    public $stackcode;
    public $membername;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stack_transaction';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'total',
                ],
                'value' => function ($event) {
                    return $this->price * $this->volume;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'member_id',
                ],
                'value' => function ($event) {
                    if (Yii::$app->user->identity->isAdmin()) {
                        $existUser = Member::find()->where(['=', 'username', $this->membername])->one();
                        return $existUser ? $existUser->id : 0;
                    } else {
                        return Yii::$app->user->identity->id;
                    }
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'stack_id',
                ],
                'value' => function ($event) {
                    $stack = Stack::find()->where(['=', 'code', $this->stackcode])->one();
                    return $stack ? $stack->id : 0;
                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'charge',
                ],
                'value' => function ($event) {
                    if ($this->type) {
                        return $this->price * $this->volume * 0.001;
                    } else {
                        return 0;
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
            [['membername', 'stackcode', 'volume',], 'required'],
            [[ 'stack_id', 'member_id', 'volume', 'type'], 'integer'],
            [['price', 'total'], 'number'],
            [['membername'], 'checkUsername'],
            [['stackcode'], 'checkStackcode'],
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
            'member_id' => Yii::t('app', 'Member ID'),
            'volume' => Yii::t('app', 'Volume'),
            'type' => Yii::t('app', 'Transaction Type'),
            'price' => Yii::t('app', 'In Price'),
            'total' => Yii::t('app', 'Total Price'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'type' => Yii::t('app', 'Stack Type'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStack()
    {
        return $this->hasOne(Stack::className(), ['id' => 'stack_id']);
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
    public function checkStackcode($attribute, $param)
    {
        $existUser = Stack::find()->where(['=', 'code', $this->stackcode])->one();
        if(!$existUser){
            $this->addError($attribute, '该股票不存在，请核对后重新输入');
        }
    }
}
