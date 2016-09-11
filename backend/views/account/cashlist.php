<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="b_download">
        <?= Html::a('下载最近一周', '/account/exportcash?week=1') ?>
        <?= Html::a('下载筛选数据', '/account/exportcash', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            [
                'attribute' => 'membername',
                'value'  => function($model) {
                    return $model->getMember()->one()->username;
                }
            ],
            [
                'attribute' => 'bank',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('bank', $model->bank);
                },
                'filter' => Yii::$app->options->getOptions('bank',true),
            ],
            'cardname',
            'cardnumber',
            [
                'attribute' => 'backaddress',
                'label'=>'开户行',
            ],
            [
                'attribute' => 'amount',
                'filter' => false,
                'format' => 'decimal',
            ],
            [
                'attribute' => 'fee',
                'filter' => false,
                'format' => 'decimal',
            ],
            [
                'attribute' => 'real_amount',
                'filter' => false,
                'format' => 'decimal',
            ],
            [
                'attribute' => 'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('cash_status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('cash_status',true),
                'filterType'=>GridView::FILTER_SELECT2,
            ],
            [
                'attribute' => 'id',
                'label' => '操作',
                'filter' => false,
                'hiddenFromExport' => true,
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return (in_array($model->status, array(2, 3))) ? '' :( Html::a('发放', '/account/approve?id='.$model->id, ['data-confirm'=>"你确定要发放[" . $model->getMember()->one()->username. "]"  . $model->amount . "的提现申请"]) . '   ' . Html::a('拒绝', '/account/reject?id='.$model->id, ['data-confirm'=>"你确定要拒绝[" . $model->getMember()->one()->username. "]"  . $model->amount . "的提现申请"]) );
                }
            ],
        ],
    ]); ?>

</div>
