<?php

namespace api\modules\v1\models;


use common\models\Log;
use common\models\Member;
use common\models\System;
use Yii;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
/**
 * Country Model
 *
 * @author Budi Irawan <deerawan@gmail.com>
 */
class Account extends ActiveRecord
{
    public $refer_id;
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
            [['created_at', 'updated_at'], 'safe'],
            [['note', 'refer_id'], 'string', 'max' => 250]
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
            'membername' => Yii::t('app', 'Member Name'),
            'type' => Yii::t('app', 'In Type'),
            'note' => Yii::t('app', 'Note'),
            'created_at' => Yii::t('app', 'Created At'),
            'updated_at' => Yii::t('app', 'Updated At'),
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $member = Member::findOne(array('username' => $this->member_id));
        if ($member) {
            $this->member_id = $member->id;
            $this->account_type = 1;
            $this->fee = 0;
            $this->type = 3;
            $member->finance_fund += $this->amount;
            $this->total = $member->finance_fund;
            $this->note = '投资网站资金转移, 对方系统转出纪录ID:' . $this->refer_id;
            if ($member->save(false, array('finance_fund'))) {
                return parent::save($runValidation, $attributeNames);
            } else {
                Log::add('API', '转账', json_encode($member->getErrors()));
                $this->addError('member_id', '转账失败.');
            }
        } else {
            $this->addError('member_id', '股票会员编号不存在.');
        }
        return $this;
    }
}