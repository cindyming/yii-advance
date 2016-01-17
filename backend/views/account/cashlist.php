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
            [
                'attribte' => 'amount',
                'format' => 'decimal',
            ],
            [
                'attribte' => 'fee',
                'format' => 'decimal',
            ],
            [
                'attribte' => 'real_amount',
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
            ],
            [
                'attribute' => 'status',
                'label' => '操作',
                'hiddenFromExport' => true,
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return (in_array($model->status, array(2, 3))) ? '' :( Html::a('发放', '/account/approve?id='.$model->id, ['data-confirm'=>"你确定要发放[" . $model->getMember()->one()->username. "]"  . $model->amount . "的提现申请"]) . '   ' . Html::a('拒绝', '/account/reject?id='.$model->id, ['data-confirm'=>"你确定要拒绝[" . $model->getMember()->one()->username. "]"  . $model->amount . "的提现申请"]) );
                }
            ],
        ],
    ]); ?>

</div>
