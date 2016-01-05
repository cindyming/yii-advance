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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            'volume',
            'price',
            'total_price',
            'charge',
            'created_at',
            [
                'attribute' => 'status',
                'filter' => Yii::$app->options->getOptions('transcation_status'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('transcation_status', $model->status);
                }
            ],
        ],
    ]); ?>

</div>
