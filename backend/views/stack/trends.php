<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stack Trends');
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
                'attribute' => 'stack_id',
                'value' => function($model) {
                    return $model->getCode();
                },
                'filter' =>  \common\models\Stack::getStackCodeOptions(),
            ],
            [
                'attribute' => 'name',
                'filter' => false,
                'value' => function($model) {
                    return $model->getName();
                }
            ],
            [
                'attribute' => 'price',
                'filter' => false,
            ],
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
        ],
    ]); ?>

</div>
