<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MessageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Messages');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="message-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'type',
                'filter' => Yii::$app->options->getOptions('question_type'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('question_type', $model->type);
                }
            ],
            'title',
            [
                'attribute' => 'member_id',
                'value' => function($model) {
                    return $model->getMember()->one()->username;
                }
            ],
            [
                'attribute' => 'replied_content',
                'label' => '是否回复',
                'value' => function($model) {
                    return $model->isReplied();
                }
            ],
            'created_at',
            [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return ($model->replied_content == '') ? '' :  $model->updated_at;
                }
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '删除',
                'content' => function($model) {
                    return Html::a('删除', ['delete', 'id' => $model->id]);
                }
            ],
            [
                'class' => 'yii\grid\Column',
                'header' => '回复',
                'content' => function($model) {
                    return Html::a('回复', ['update', 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

</div>
