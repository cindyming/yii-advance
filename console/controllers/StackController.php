<?php

namespace console\controllers;

use common\models\InRecord;
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
            }

            $transactions = StackTransaction::find()->where(['=', 'status', 0])->andWhere(['<', 'created_at', $date->date])->all();
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
        $transaction->status = 1;
        $memberStack = $transaction->getMemberStack()->one();
        $memberStack->sell_volume += $transaction->volume;
        $memberStack->lock_volume -= $transaction->volume;
        $memberStack->save();
        $transaction->save();

    }
    protected function dealSellAction($transaction)
    {
        $transaction->status = 1;
        $memberStack = $transaction->getMemberStack()->one();
        $memberStack->lock_volume -= $transaction->volume;
        $member = $transaction->getMember()->one();
        $fee = $transaction->total_price * 0.01;
        $member->finance_fund += ($transaction->total_price - $fee);
        $stackOutRecord = InRecord::prepareModelForSellStack($transaction->member_id, ($transaction->total_price - $fee),$member->finance_fund, $fee);
        $stackOutRecord->save();
        $member->save();
        $memberStack->save();
        $transaction->save();
    }
}