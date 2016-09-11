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
?>
<div class="stack-index">

    <h1><?= Html::encode($this->title) ?></h1>


    <div class="b_download">
        <?= Html::a('下载筛选数据', '/stack/fundexport', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => $gridColumns
    ]); ?>

</div>
