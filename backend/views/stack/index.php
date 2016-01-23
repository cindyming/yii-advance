<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stacks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create Stack'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>


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
                'class' => 'kartik\grid\EditableColumn',
                'attribute'=>'price',
                'editableOptions'=> function ($model, $key, $index) {
                    return [
                        'inputType' => \kartik\editable\Editable::INPUT_TEXT,
                        'size'=>'sm',
                    ];
                },
                'filter' => false,

            ],

            [
                'attribute' =>  'updated_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' =>  'created_at',
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
                'header' => '修改',
                'template' => '{update}',
            ]
        ],
    ]); ?>

</div>
