<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "stack_authorize".
 *
 * @property integer $id
 * @property integer $stack_id
 * @property string $price
 * @property string $real_price
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $member_id
 *
 * @property Member $member
 * @property Stack $stack
 */
class StackAuthorize extends \yii\db\ActiveRecord
{
    public $stackcode;
    public $membername;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stack_authorize';
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                    if ($this->status) {
                        return $this->status;
                    } else {
                        return 1;
                    }

                },
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stack_id', 'price', 'member_id', 'account_type', 'type', 'volume'], 'required'],
            [['stack_id', 'status', 'member_id'], 'integer'],
            [['price', 'real_price'], 'number'],
            [['membername'], 'checkUsername'],
            [['stackcode'], 'checkStackcode'],
            [['created_at', 'updated_at', 'membername', 'stackcode', 'note', 'status', 'account_type'], 'safe']
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
            'price' => Yii::t('app', 'Authorize Price'),
            'real_price' => Yii::t('app', 'Real Price'),
            'status' => Yii::t('app', 'Status'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'member_id' => Yii::t('app', 'Member ID'),
            'membername' => Yii::t('app', 'Member Name'),
            'stackcode' => Yii::t('app', 'Stack Code'),
            'note' => Yii::t('app', 'Note'),
            'volume' => Yii::t('app', 'Volume'),
            'type' => Yii::t('app', 'Authorize Type'),
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

    public function setStackId($stack_id = null)
    {
        if ($stack_id) {
            $this->stack_id = $stack_id;
        } else {
            $stack = Stack::find()->where(['=', 'code', $this->stackcode])->one();
            if ($stack) {
                $this->stack_id = $stack->id;
            }
        }
    }

    public function checkSellVolume($memberStack, $volume)
    {
        if (!$memberStack || ($volume > $memberStack->sell_volume)) {
            $this->addError('volume', '可交易股票数量不足: '. ($memberStack ? $memberStack->sell_volume : 0));
            return false;
        } else {
            return true;
        }
    }

    public function setMemberId()
    {
        if (Yii::$app->user->identity->isAdmin()) {
            $existUser = Member::find()->where(['=', 'username', $this->membername])->one();
            $this->member_id = $existUser ? $existUser->id : 0;
        } else {
            $this->member_id = Yii::$app->user->identity->id;
        }
    }
}
