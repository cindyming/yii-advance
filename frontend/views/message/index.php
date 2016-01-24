<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Messages List');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'membername',
                'value' => function($model) {
                    return $model->getMember()->one()->username;
                },
            ],
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('question_type', $model->type);
                },
                'filter' => Yii::$app->options->getOptions('question_type')
            ],
            'title',
            [
                'attribute' => 'replied_content',
                'label' => '是否回复',
                'filter' => array(
                    0 => '不限',
                    1 => '已回复',
                    2 => '未回复'
                ),
                'value' => function($model) {
                    return $model->replied_content ? '已回复' : '未回复';
                }
            ],
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return ($model->replied_content) ?  $model->updated_at : '';
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '详情',
                'template' => '{view}'
            ],
        ],
    ]); ?>

</div>
