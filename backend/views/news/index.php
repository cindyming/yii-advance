<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\NewsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'title',
            [
                'attribute' => 'be_top',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('be_top', $model->be_top);
                },
                'filter' => Yii::$app->options->getOptions('be_top'),
            ],
            [
                'attribute' => 'public_at',
                'filter' => false,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '修改',
                'template' => '{update} {delete} {view}',
            ]
        ],
    ]); ?>

</div>
