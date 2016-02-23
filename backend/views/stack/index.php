<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stacks');
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
            'code',
            'name',
            [
                'attribute'=>'price',
                'filter' => false,
            ],

            [
                'attribute' =>  'updated_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('status',true),
            ]
        ],
    ]); ?>

</div>
