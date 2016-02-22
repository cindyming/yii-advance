<?php

namespace console\controllers;

use common\models\InRecord;
use common\models\Member;
use common\models\MemberStack;
use common\models\OutRecord;
use common\models\Stack;
use common\models\StackAuthorize;
use common\models\StackTransaction;
use common\models\Date;
use common\models\System;
use Yii;
use yii\console\Controller;
use yii\db\Expression;


class AuthorizeController extends Controller
{
    public function actionIndex()
    {
        if (Date::isWorkingDay() && Date::isWorkingTime() || true) {
            $date = date('Y-m-d H:i:s', time()-2);
            $stacks = Stack::find()->where(['=', 'status', 0])->all();
            if (count($stacks)) {
                $stackPrice = array();
                foreach ($stacks as $stack) {
                    $stackPrice[$stack->id] = $stack->price;
                }

                $authorizes = StackAuthorize::find()->where(['=', 'status', 1])->andWhere(['in', 'stack_id', array_keys($stackPrice)])->all();
                StackAuthorize::updateAll(array('status' => 4), 'status=1 AND stack_id in (' . implode(',', array_keys($stackPrice)) . ')');

                foreach ($authorizes as $auth) {
                    if ($auth->type == 0) {
                        if ($auth->price >= $stackPrice[$auth->stack_id]) {
                            $this->dealBuyAction($auth, $stackPrice[$auth->stack_id]);
                        } else {
                            $auth->status = 1;
                            $auth->save();
                        }
                    } else if ($auth->type == 1) {
                        if ($auth->price <= $stackPrice[$auth->stack_id]) {
                            $this->dealSellAction($auth, $stackPrice[$auth->stack_id]);
                        } else {
                            $auth->status = 1;
                            $auth->save();
                        }

                    }
                }
            }



        } else {
            return false;
        }
    }

    protected function dealBuyAction($auth, $price)
    {


        $member = Member::findOne($auth->member_id);

        $totalPrice = $price * $auth->volume;


        if ((($auth->account_type == 1) && $member->finance_fund < $totalPrice) ||
            (($auth->account_type == 2) && $member->stack_fund < $totalPrice)) {
            $auth->status = 3;
            $auth->note = '账户余额不足. 理财基金:.' . $member->finance_fund . '. 购股账户:'. $member->stack_fund;
            $auth->save();
        } else {
            $stack = Stack::findOne($auth->stack_id);
            $memberStack = MemberStack::getMemberStack($auth);

            $model = new StackTransaction();
            $data = array(
                'member_id' => $member->id,
                'stack_id' => $auth->stack_id,
                'price' => $price,
                'account_type' => $auth->account_type,
                'type' => 0,
                'volume' => $auth->volume,
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
                $auth->status = 2;
                $auth->real_price = $price;
                $auth->note = '成功购买[' . $stack->code . ']' . $model->volume . '股[' . date('Y-m-d H:i:s') . ']';
                $success = false;
                if ( $model->save() && $memberStack->save() && $member->save() &&  $outRecord->save() && $auth->save()) {
                    $success = true;
                }
                if ($success) {
                    $transaction->commit();
                } else {
                    $transaction->rollback();
                    $auth->note = ('委托失败');
                    $auth->note .= (json_encode($model->getErrors()));
                    $auth->note .= (json_encode($memberStack->getErrors()));
                    $auth->note .= (json_encode($member->getErrors()));
                    $auth->note .= (json_encode($outRecord->getErrors()));
                    $auth->note .= (json_encode($auth->getErrors()));
                    $auth->status = 3;
                    $auth->save();
                }

            } catch (Exception $e) {
                $auth->note = '委托失败';
                $auth->status = 3;
                $auth->save();
            }
        }


    }
    protected function dealSellAction($auth, $price)
    {
        $stack = Stack::findOne($auth->stack_id);
        $memberStack = MemberStack::getMemberStack($auth);

        $model = new StackTransaction();
        $data = array(
            'member_id' => $auth->member_id,
            'stack_id' => $auth->stack_id,
            'price' => $price,
            'account_type' => 1,
            'type' => 1,
            'volume' => $auth->volume,
        );
        $model->load($data, '');

        if (!$model->checkSellVolume($memberStack, $model->volume)) {
            $auth->status = 3;
            $auth->note = '股票可出售数量不足[' . $stack->code . ']';
            $auth->save();
        } else {
            $model->total_price = $model->price * $model->volume;
            $memberStack->sell_volume -= $model->volume;
            $memberStack->lock_volume += $model->volume;
            $auth->status = 2;
            $auth->real_price = $price;
            $auth->note = '成功出售[' . $stack->code . ']' . $model->volume . '股[' . date('Y-m-d H:i:s') . ']';

            $connection = Yii::$app->db;
            try {
                $transaction = $connection->beginTransaction();
                if ($model->save() && $memberStack->save() && $auth->save()) {
                    $transaction->commit();
                } else {
                    Yii::error('Sell Stack Failed');
                    Yii::error(json_encode($model->getErrors()));
                    Yii::error(json_encode($memberStack->getErrors()));
                    $transaction->rollback();
                    $auth->note = '委托失败';
                    $auth->note .= (json_encode($model->getErrors()));
                    $auth->note .= (json_encode($memberStack->getErrors()));
                    $auth->status = 3;
                    $auth->save();
                }
            } catch (Exception $e) {
                $auth->note = '委托失败';
                $auth->status = 3;
                $auth->save();
            }
        }
    }
}