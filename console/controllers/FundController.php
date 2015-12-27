<?php

namespace console\controllers;

use common\models\InRecord;
use common\models\FundTransaction;
use common\models\Date;
use Yii;
use yii\console\Controller;
use yii\db\Expression;


class FundController extends Controller
{
    public function actionIndex()
    {
        if(Date::isWorkingDay()) {
            $transactions = FundTransaction::find()->where(['=', 'locked', 1])->andWhere(['=', 'cleared', 0])->all();
            foreach ($transactions as $transaction) {
                $amount = $transaction->investment * $transaction->fund->daily;
                $transaction->revenue += $amount;
                $member = $transaction->member;
                $member->finance_fund += $amount;
                $inRecord = new InRecord();
                $data = array(
                    'member_id' => $transaction->member_id,
                    'type' => 5,
                    'fee' => 0,
                    'amount' => $amount,
                    'total' => $member->finance_fund,
                    'account_type' => 1,
                    'note' => '基金分红: (' . date('Y-m-d', time()) . ')'
                );
                $inRecord->load($data, '');
                $inRecord->save();
                $member->save();
                $transaction->save();
            }
        }

    }

}