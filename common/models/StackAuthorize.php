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
            [['stack_id', 'price', 'member_id', 'type', 'volume'], 'required'],
            [['stack_id', 'status',  'account_type','member_id'], 'integer'],
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


    public static function dealAuth($stack)
    {
        if (Date::isWorkingDay() && Date::isWorkingTime() || true) {
            $stackId = $stack->id;
            $stackPrice = $stack->price;
            $inAuthrizes = StackAuthorize::find()->where(['=', 'status', 1])
                ->andWhere(['=', 'stack_id', $stackId])
                ->andWhere(['=', 'type', 0])
                ->andWhere(['>=', 'price', $stackPrice])->all();
            StackAuthorize::updateAll(array('status' => 4), "status=1 AND stack_id={$stackId} AND type=0 AND price>={$stackPrice}");
            foreach ($inAuthrizes as $auth) {
                if ($auth->price >= $stackPrice) {
                    $auth->dealBuyAction($stackPrice);
                }
            }

            $inAuthrizes = StackAuthorize::find()->where(['=', 'status', 1])
                ->andWhere(['=', 'stack_id', $stackId])
                ->andWhere(['=', 'type', 1])
                ->andWhere(['<=', 'price', $stackPrice])->all();
            StackAuthorize::updateAll(array('status' => 4), "status=1 AND stack_id={$stackId} AND type=1 AND price<={$stackPrice}");
            foreach ($inAuthrizes as $auth) {
                if ($auth->price <= $stackPrice) {
                    $auth->dealSellAction($stackPrice);
                }
            }
        }
    }

    protected function dealBuyAction($price)
    {


        $member = Member::findOne($this->member_id);

        $totalPrice = $price * $this->volume;

        if ((($this->account_type == 1) && $member->finance_fund < $totalPrice) ||
            (($this->account_type == 2) && $member->stack_fund < $totalPrice)) {
            $this->status = 3;
            $this->note = '账户余额不足. 理财基金:.' . $member->finance_fund . '. 购股账户:'. $member->stack_fund;
            $this->save();
        } else {
            $stack = Stack::findOne($this->stack_id);
            $memberStack = MemberStack::getMemberStack($this);

            $model = new StackTransaction();
            $data = array(
                'member_id' => $member->id,
                'stack_id' => $this->stack_id,
                'price' => $price,
                'account_type' => $this->account_type,
                'type' => 0,
                'volume' => $this->volume,
            );
            $model->load($data, '');
            $model->total_price = $model->price * $model->volume;
            if ($model->account_type == 1) {
                $member->finance_fund -= $model->total_price;
                $outRecord = OutRecord::prepareModelForBuyStack($model->member_id, $model->total_price, $member->finance_fund, 1);

            } else {
                $member->stack_fund -= $model->total_price;
                $outRecord = OutRecord::prepareModelForBuyStack($model->member_id, $model->total_price, $member->stack_fund, 2);
            }
            $outRecord->note = '股买[' . $stack->code . ']' . $model->volume . '股';

            $connection = Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();
                $this->status = 2;
                $this->real_price = $price;
                $this->note = '成功购买[' . $stack->code . ']' . $model->volume . '股[' . date('Y-m-d H:i:s') . ']';
                $success = false;
                if ( $model->save() && $memberStack->save() && $member->save() &&  $outRecord->save() && $this->save()) {
                    $model = StackTransaction::findOne($model->id);
                    $date = date('Y-m-d H:i:s', strtotime($model->created_at)+rand(1,5));
                    $outRecord->created_at = $date;
                    $model->created_at = $date;
                    $outRecord->save();
                    $model->save();
                    $success = true;
                }
                if ($success) {
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                    $this->note = ('委托失败');
                    $this->note .= (json_encode($model->getErrors()));
                    $this->note .= (json_encode($memberStack->getErrors()));
                    $this->note .= (json_encode($member->getErrors()));
                    $this->note .= (json_encode($outRecord->getErrors()));
                    $this->note .= (json_encode($this->getErrors()));
                    $this->status = 3;
                    $this->save();
                }

            } catch (Exception $e) {
                $transaction->rollback();
                $this->note = '委托失败';
                $this->status = 3;
                $this->save();
            }
        }


    }
    protected function dealSellAction($price)
    {
        $stack = Stack::findOne($this->stack_id);
        $memberStack = MemberStack::getMemberStack($this);

        $time = time() + rand(1,5);
        $date = date('Y-m-d H:i:s', $time);

        $model = new StackTransaction();
        $data = array(
            'member_id' => $this->member_id,
            'stack_id' => $this->stack_id,
            'price' => $price,
            'account_type' => 1,
            'type' => 1,
            'volume' => $this->volume,
            'created_at' => $date
        );
        $model->load($data, '');

        if (!$model->checkSellVolume($memberStack, $model->volume)) {
            $this->status = 3;
            $this->note = '股票可出售数量不足[' . $stack->code . ']';
            $this->save();
        } else {
            $model->total_price = $model->price * $model->volume;
            $memberStack->sell_volume -= $model->volume;
            $memberStack->lock_volume += $model->volume;
            $this->status = 2;
            $this->real_price = $price;
            $this->note = '成功出售[' . $stack->code . ']' . $model->volume . '股[' . date('Y-m-d H:i:s') . ']';

            $connection = Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();
                if ($model->save() && $memberStack->save() && $this->save()) {
                    $transaction->commit();
                    $model = StackTransaction::findOne($model->id);
                    $date = date('Y-m-d H:i:s', strtotime($model->created_at)+rand(1,5));
                    $model->created_at = $date;
                    $model->save();
                } else {
                    Yii::error('Sell Stack Failed');
                    Yii::error(json_encode($model->getErrors()));
                    Yii::error(json_encode($memberStack->getErrors()));
                    $transaction->rollback();
                    $this->note = '委托失败';
                    $this->note .= (json_encode($model->getErrors()));
                    $this->note .= (json_encode($memberStack->getErrors()));
                    $this->status = 3;
                    $this->save();
                }
            } catch (Exception $e) {
                $transaction->rollback();
                $this->note = '委托失败';
                $this->status = 3;
                $this->save();
            }
        }
    }
}
