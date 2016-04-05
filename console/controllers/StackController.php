<?php

namespace console\controllers;

use common\models\InRecord;
use common\models\Member;
use common\models\StackTransaction;
use common\models\Date;
use common\models\System;
use Yii;
use yii\console\Controller;
use yii\db\Expression;


class StackController extends Controller
{
    public function actionIndex()
    {
        if (Date::isWorkingDay()) {
            $limit = (int)System::loadConfig('transaction_rule');
            if ($limit) {
                $limit = $limit - 1;
            } else {
                $limit = 0;
            }
            if ($limit == 0) {
                $date = date('Y-m-d');
            } else {
                $dates = Date::find()->where(['<', 'date', new Expression('curdate()')])->andWhere(['=', 'status', 0])->orderBy(['date' => SORT_DESC])->limit(2)->all();
                $date = array_pop($dates);
                $date = $date->date;
            }
            $date .= ' 23:59:59';var_dump($date);
            $transactions = StackTransaction::find()->where(['=', 'status', 0])->andWhere(['<', 'created_at', $date])->all();
            foreach ($transactions as $transaction) {
                if ($transaction->type == 0) {
                    $this->dealBuyAction($transaction);
                } else {
                    $this->dealSellAction($transaction);
                }

            }

        } else {
            return false;
        }
    }

    protected function dealBuyAction($transaction)
    {
        $member = Member::findOne($transaction->member_id);
        if (($member->finance_fund  >= 0) && ($member->stack_fund  >= 0)) {
            $transaction->status = 1;
            $memberStack = $transaction->getMemberStack()->one();
            $memberStack->sell_volume += $transaction->volume;
            $memberStack->lock_volume -= $transaction->volume;
            $memberStack->save();
            $transaction->save();
        } else if (false){
            $transaction->status = 2;
            $memberStack = $transaction->getMemberStack()->one();
            $memberStack->lock_volume -= $transaction->volume;
            $fee = 0;
            $member->finance_fund += ($transaction->total_price - $fee);
            $stackOutRecord = InRecord::prepareModelForSellStack($transaction->member_id, ($transaction->total_price - $fee),$member->finance_fund, $fee);
            $stackOutRecord->note = '股票购买失败 系统退回['  . $transaction->created_at .  ']购买[' . $transaction->stack->code . ']' . $transaction->volume . '股';
            $stackOutRecord->type = 5;
            $stackOutRecord->save();
            $memberStack->save();
            $transaction->save();
        }


    }
    protected function dealSellAction($transaction)
    {
        $transaction->status = 1;
        $memberStack = $transaction->getMemberStack()->one();
        $memberStack->lock_volume -= $transaction->volume;
        $member = $transaction->getMember()->one();
        $fee = round($transaction->total_price * System::loadConfig('sell_fee_rate'), 2);
        $member->finance_fund += ($transaction->total_price - $fee);
        $stackOutRecord = InRecord::prepareModelForSellStack($transaction->member_id, ($transaction->total_price - $fee),$member->finance_fund, $fee);
        $stackOutRecord->note = '系统解锁['  . $transaction->created_at .  ']出售[' . $transaction->stack->code . ']' . $transaction->volume . '股';
        $stackOutRecord->save();
        $member->save();
        $memberStack->save();
        $transaction->save();
    }
}
