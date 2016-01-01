<?php

namespace console\controllers;

use common\models\Member;
use common\models\OutRecord;
use Yii;
use yii\console\Controller;


class MemberController extends Controller
{
    public function actionIndex()
    {
        $members = Member::findAll(array());
        foreach ($members as $member) {
            $member->finance_fund -= System::loadConfig('annual_fee');
            $outRecord = OutRecord::prepareYearlyFeeRecord($member->id, $member->finance_fund );
            $member->save();
            $outRecord->save();
        }

    }

}