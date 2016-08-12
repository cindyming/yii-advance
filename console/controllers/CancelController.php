<?php

namespace console\controllers;

use common\models\InRecord;
use common\models\Member;
use common\models\StackTransaction;
use common\models\Date;
use common\models\System;
use Yii;
use yii\base\Exception;
use yii\console\Controller;
use yii\db\Expression;


class CancelController extends Controller
{
    public function actionIndex()
    {
        $transactions = StackTransaction::find()->where(['=', 'status', 0])
            ->andWhere(['=', 'stack_id', 2])
            ->andWhere(['=', 'type', 0])
            ->andWhere(['=', 'price', 3.22])
            ->andWhere(['>', 'created_at', '2016-08-11 15:00:00'])->all();
        var_dump('总数:' . count($transactions));
        $i = 0;
        foreach ($transactions as $transaction) {
            try {
                $str = $transaction->cancelSell();
                var_dump($str);
                $i ++;
            } catch (Exception $e) {
                var_dump($e->getMessage());
            }
        }

        var_dump('成功撤销:'. $i);
    }

}
