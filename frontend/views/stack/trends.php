<?php

$this->params['breadcrumbs'][] = $this->title;


$this->title = Yii::t('app', 'Stack Trends');
?>

<div class="stack-index">

    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

<?php

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>


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
                'attribute' =>  'created_at'
            ],
        ],
    ]); ?>


</div>
