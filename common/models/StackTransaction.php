<?php

namespace common\models;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\MemberStack;

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
    public $stackprice;
    public $total;
    public $password2;
    public $sellnumber;
    public $locknumber;
    public $account_type;
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
                    ActiveRecord::EVENT_BEFORE_INSERT => 'status',
                ],
                'value' => function ($event) {
                    if ($this->status) {
                        return $this->status;
                    } else {
                        return 0;
                    }

                },
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'charge',
                ],
                'value' => function ($event) {
                    if ($this->type) {
                        return round($this->total_price * System::loadConfig('sell_fee_rate'), 2);
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
            [[ 'volume',], 'required'],
            [[ 'stack_id', 'member_id', 'volume', 'type', 'status'], 'integer'],
            [['price', 'total_price'], 'number'],
            [['membername'], 'checkUsername'],
            [['stackcode'], 'checkStackcode'],
            [['created_at', 'updated_at','membername', 'stackcode', 'account_type', 'password2', 'note'], 'safe']
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
            'sellnumber' => Yii::t('app', 'Sell Number'),
            'locknumber' => Yii::t('app', 'Lock Number'),
            'type' => Yii::t('app', 'Transaction Type'),
            'price' => Yii::t('app', 'In Price'),
            'total_price' => Yii::t('app', 'Total Price'),
            'status' => Yii::t('app', 'Exchange Status'),
            'charge' => Yii::t('app', 'Charge'),
            'membername' => Yii::t('app', 'Member Name'),
            'password2' => Yii::t('app', 'Password2'),
            'stackcode' => Yii::t('app', 'Stack Code'),
            'stackname' => Yii::t('app', 'Stack Name'),
            'stackprice' => Yii::t('app', 'Stack Price'),
            'created_at' => Yii::t('app', 'Stack Created At'),
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
    public function getMemberStack()
    {
        return $this->hasOne(MemberStack::className(), ['member_stack.member_id' => 'member_id', 'member_stack.stack_id' => 'stack_id']);
    }

    public function checkSellVolume($memberStack, $volume)
    {
        if ($volume > $memberStack->sell_volume) {
            $this->addError('volume', '可交易股票数量不足: '. $memberStack->sell_volume);
            return false;
        } else {
            return true;
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
    public function setMemberId()
    {
        if (Yii::$app->user->identity->isAdmin()) {
            $existUser = Member::find()->where(['=', 'username', $this->membername])->one();
            $this->member_id = $existUser ? $existUser->id : 0;
        } else {
            $this->member_id = Yii::$app->user->identity->id;
        }
    }

    public function cancelSell() {

        $model = MemberStack::find()->where(['=', 'member_id', $this->member_id])->andWhere(['=', 'stack_id', $this->stack_id])->one();
        $str = '撤销' . $this->member_id . ', ' . $this->stack_id . ':' . $this->volume;
        $model->lock_volume -= $this->volume;
        $model->sell_volume += $this->volume;
        if (($model->lock_volume >= 0)  && ($model->sell_volume >= 0)) {
            $this->status = 2;
            if ($model->save() && $this->save()) {

            } else {
                throw new Exception(Log::arrayToString($model->getErrors()) . Log::arrayToString($this->getErrors()));
            }
        } else {
            throw new Exception('可扣的数量不对,请联系管理员, ' . $this->member_id . $model->lock_volume . $model->sell_volume);
        }

        return $str;
    }

    public function cancelBuy() {


        $model = MemberStack::find()->where(['=', 'member_id', $this->member_id])->andWhere(['=', 'stack_id', $this->stack_id])->one();
        $nameOption = Stack::getStackNameOptions();
        $str = '管理员撤销' . $this->member_id . ',购买 ' . (isset($nameOption[$this->stack_id]) ? $nameOption[$this->stack_id] : ''). ':' . $this->volume . '. ' . $this->created_at;

        if (($model->lock_volume >= 0)) {
            $this->status = 2;
            $connection = Yii::$app->db;
            try {

                $member = Member::findOne($this->member_id);
                $member->finance_fund += $this->total_price;
                $model->lock_volume -= $this->volume;

                $data = array(
                    'member_id' => $this->member_id,
                    'type' => 4,
                    'fee' => 0,
                    'amount' => $this->total_price,
                    'total' => $member->finance_fund,
                    'account_type' => 1,
                    'note' => $str
                );

                $inRecord = new InRecord();
                $inRecord->load($data, '');

                $transaction = $connection->beginTransaction();
                if ($model->save() && $this->save() && $inRecord->save() && $member->save()) {
                    $transaction->commit();
                } else {
                    $transaction->rollBack();
                    throw new Exception(Log::arrayToString($model->getErrors()) . Log::arrayToString($this->getErrors()));
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                throw new Exception($e->getMessage());
            }
        } else {
            throw new Exception('可扣的数量不对,请联系管理员, ' . $this->member_id . $model->lock_volume . $model->sell_volume);
        }

        return $str;
    }
}
