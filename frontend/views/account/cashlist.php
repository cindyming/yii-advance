<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\CashSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '提现记录';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            'amount',
            'fee',
            'real_amount',
            [
                'attribute' => 'created_at',
                'filter' => true,
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('cash_status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('cash_status',true),
            ],
        ],
    ]); ?>

</div>
