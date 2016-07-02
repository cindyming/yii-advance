<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->username;
?>
<div class="member-view">

    <h1><?= Html::encode($model->username) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'username',
            'status',
            'nickname',
            'identity',
            'phone',
            'title',
            'investment',
            'bank',
            'cardname',
            'cardnumber',
            'bankaddress',
            'email:email',
            'qq',
            'stack_fund',
            'finance_fund',
        ],
    ]) ?>

</div>
