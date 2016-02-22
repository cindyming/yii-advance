<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stacks List');
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
                'attribute' => 'price',
                'filter' => false,
            ],
            [
                'attribute' =>  'updated_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('status',true),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{buy}',
                'buttons' => [
                    'buy' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '购买'),
                            'aria-label' => Yii::t('yii', '购买'),
                        ];
                        return ($model->status) ? '' : Html::a('购买', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'buy') {
                        $url = '/authorize/create?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
