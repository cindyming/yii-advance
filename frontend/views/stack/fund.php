<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'My Stacks');
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
            'sell_volume',
            'lock_volume',
            [
                'attribute' => 'current_price',
                'value' => function($model) {
                    return $model->stack->price;
                }
            ],
            [
                'attribute' => 'total_price',
                'value' => function($model) {
                    return $model->stack->price * ($model->sell_volume + $model->lock_volume);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '操作',
                'template' => '{sell}',
                'buttons' => [
                    'sell' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '卖出'),
                            'aria-label' => Yii::t('yii', '卖出'),
                        ];
                        return Html::a('卖出', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'sell') {
                        $url = '/stack/sell?id='.$model->stack->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
