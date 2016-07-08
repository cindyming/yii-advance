<?php


$dependency = [
    'class' => 'yii\caching\DbDependency',
    'sql' => 'SELECT COUNT(id) FROM stack_trends',
];

$variations = [
    Yii::$app->request->get('page', 1),
    Yii::$app->request->get('StackSearch', array())
];


$this->title = Yii::t('app', 'Stacks List');
?>

<?php if ($this->beginCache('stack_list', ['dependency' => $dependency, 'variations' => $variations])): ?>

<?php
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-index">

    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

    <?= yii\grid\GridView::widget([
        'dataProvider' => $dataProvider,
//        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
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
                'filter' => false,
            ],
            // [
            //     'attribute' => 'status',
            //     'value' => function($model) {
            //         return Yii::$app->options->getOptionLabel('status', $model->status);
            //     },
            //     'filter' => Yii::$app->options->getOptions('status',true),
            // ],
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
                        return ($model->status) ? '' : yii\helpers\Html::a('购买', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'buy') {
                        $url = '/stack/buy?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>


<?php

$this->endCache();

endif;
?>
