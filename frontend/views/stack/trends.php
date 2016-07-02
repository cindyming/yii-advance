<?php


$dependency = [
    'class' => 'yii\caching\DbDependency',
    'sql' => 'SELECT COUNT(id) FROM stack_trends',
];

$variations = [
    Yii::$app->request->get('page', 1),
    Yii::$app->request->get('StackTrendsSearch', array())
];

$this->title = Yii::t('app', 'Stack Trends');

if ($this->beginCache('stack_trends', ['dependency' => $dependency, 'variations' => $variations])):
?>
<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-index">

    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'stack_id',
                'header' => '股票代码',
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
                'filterType'=>kartik\grid\GridView::FILTER_DATE_RANGE,
            ],
        ],
    ]); ?>

</div>
<?php

$this->endCache();
?>

<?php endif ?>
