<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stack Transactions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="b_download">
        <?= Html::a('下载最近一周', '/stack/export?week=1') ?>
        <?= Html::a('下载筛选数据', '/stack/export', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => '保存为Excel文件']
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
        ],
        'autoXlFormat' => true,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'membername',
                'value' => function($model) {
                    return $model->member->username;
                }
            ],
            [
                'attribute' => 'type',
                'filter' => Yii::$app->options->getOptions('stack_type'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('stack_type', $model->type);
                }
            ],
            [
                'attribute' => 'stackcode',
                'value' => function($model) {
                    return $model->stack->code;
                }
            ],
            [
                'attribute' => 'stackname',
                'value' => function($model) {
                    return $model->stack->name;
                }
            ],
            [
                'attribute' => 'volume',
                'filter' => false,
            ],
            [
                'attribute' => 'price',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'total_price',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'charge',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'status',
                'filter' => Yii::$app->options->getOptions('transcation_status'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('transcation_status', $model->status);
                }
            ],
            [
                'attribute' => 'status',
                'label' => '操作',
                'filter' => false,
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return ($model->status) ? '' :( Html::a('手动解锁', '/stack/unlock?id='.$model->id, ['data-confirm'=>"你确定要解锁"  . $model->member->username ."[" . $model->stack->code . "]" . $model->volume ."股的交易"]));
                }
            ],
        ],
    ]); ?>

</div>
