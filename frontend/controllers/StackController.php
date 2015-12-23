<?php

namespace frontend\controllers;

class StackController extends \yii\web\Controller
{
    public function actionBuy()
    {
        return $this->render('buy');
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTransactions()
    {
        return $this->render('transactions');
    }

}
