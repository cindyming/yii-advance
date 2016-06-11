<?php

namespace console\controllers;

use common\models\Health;
use common\models\InRecord;
use common\models\Member;
use common\models\MemberStack;
use common\models\StackTransaction;
use common\models\Date;
use common\models\System;
use Yii;
use yii\console\Controller;
use yii\db\Expression;


class HealthcheckerController extends Controller
{

    public function actionIndex()
    {
        $date = date('Y-m-d H:i:s',  strtotime("-24 hour"));

        $trans = Yii::$app->db->createCommand("SELECT member_id, stack_id FROM stack_transaction group by member_id, stack_id")->query();

        foreach ($trans as $tr) {
            $member_id = $tr['member_id'];
            $stack_id = $tr['stack_id'];
            echo $member_id . '--------' . $stack_id . PHP_EOL;
            $allTrans = Yii::$app->db->createCommand("SELECT member_id, stack_id, sum(volume) as sum_volume, status, `type` FROM stack_transaction WHERE member_id={$member_id} AND stack_id={$stack_id}  group by type, status")->query();
            $memberStack = MemberStack::find()->where(['=', 'member_id', $member_id])->andWhere(['=', 'stack_id', $stack_id])->one();
            $finishBuy = $finishSell = $lockBuy = $lockSell = 0;

            foreach ($allTrans as $t) {
                $volume = intval($t['sum_volume']);
                if ($t['type'] == 0) {
                    if ($t['status'] == 0) {
                        $lockBuy += $volume;
                    } else if ($t['status'] == 1) {
                        $finishBuy += $volume;
                    }
                } else if ($t['type'] == 1) {
                    if ($t['status'] == 0) {
                        $lockSell += $volume;
                    } else if ($t['status'] == 1) {
                        $finishSell += $volume;
                    }
                }
            }

            $exchangeVolume = $memberStack->sell_volume;
            $lockVolume = $memberStack->lock_volume;

            $error = false;
            $note = '';

            if (($finishBuy) != ($lockSell + $finishSell + $exchangeVolume)) {
                $error = true;
                $note .= (($finishBuy + $lockBuy) > ($lockSell + $finishSell + $exchangeVolume)) ? '购买的数量总数大于出售的总数. ' : '出售的总数大于购买的. ';
            }

            if (($exchangeVolume + $lockSell) != ($finishBuy - $finishSell)) {
                $error = true;
                $note .= (($finishBuy - $finishSell) > $exchangeVolume) ? '可交易数量少了. ' : '可交易数量多了. ';
            }

            if ($lockVolume != ($lockBuy + $lockSell)) {
                $error = true;
                $note .= (($lockBuy + $lockSell) > $lockVolume) ? '锁定数量少了. ' : '锁定数量多了. ';
            }

            if ($error) {
                $health = new Health();
                $data = array(
                    'member_id' => $member_id,
                    'stack_id' => $stack_id,
                    'finish_buy' => $finishBuy,
                    'finish_sell' => $finishSell,
                    'lock_sell' => $lockSell,
                    'lock_buy' => $lockBuy,
                    'exchange_total' => $exchangeVolume,
                    'lock_total' => $lockVolume,
                    'note' => $note
                );
                $health->load($data, '');
                $health->save();
            }

        }
    }
}
