<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\Newssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News List');
?>


<?php
$dependency = [
'class' => 'yii\caching\DbDependency',
'sql' => 'SELECT COUNT(id) FROM news',
];

$variations = [
Yii::$app->request->get('page', 1),
];

?>

<?php if ($this->beginCache('new_list', ['dependency' => $dependency, 'variations' => $variations])): ?>

<div class="news-index">

    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

    <?= kartik\grid\GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '编号'
            ],
            [
                'attribute' => 'be_top',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('be_top', $model->be_top);
                },
                'filter' => Yii::$app->options->getOptions('be_top'),
            ],
            'title',
            [
                'attribute' => 'public_at',
                'filter' => false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>

    <?php

    $this->endCache();

endif;
?>
