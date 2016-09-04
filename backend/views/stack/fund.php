<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stacks Record');
$this->params['breadcrumbs'][] = $this->title;

$gridColumns = [
    ['class' => 'yii\grid\SerialColumn'],
    [
        'attribute' => 'membername',
        'value' => function($model) {
            return $model->member->username;
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
        'attribute' => 'sell_volume',
        'filter' => false,
    ],
    [
        'attribute' => 'lock_volume',
        'filter' => false,
    ],
    [
        'attribute' => 'current_price',
        'format' => 'decimal',
        'value' => function($model) {
            return $model->stack->price;
        }
    ],
    [
        'attribute' => 'total_price',
        'format' => 'decimal',
        'value' => function($model) {
            return $model->stack->price * ($model->sell_volume + $model->lock_volume);
        }
    ],
];

$fullExportMenu = ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumns,
    'target' => ExportMenu::TARGET_BLANK,
    'fontAwesome' => true,
    'showConfirmAlert' => false,
    'showColumnSelector' => false,
    'pjaxContainerId' => 'kv-pjax-container',
    'dropdownOptions' => [
        'label' => 'Full',
        'class' => 'btn btn-default',
        'itemsBefore' => [
            '<li class="dropdown-header">导出全部筛选的数据</li>',
        ],
    ]
]);

?>
<div class="stack-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'export' => [
            'label' => '保存当前页数据',
            'fontAwesome' => true,
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => '保存为Excel文件']
        ],
        'toolbar'=>[
            '{export}',
            $fullExportMenu,
            '{toggleData}'
        ],
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
        ],
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => $gridColumns
    ]); ?>

</div>
