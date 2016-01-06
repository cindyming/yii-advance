<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Newssearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'News List');
?>
<div class="news-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
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
