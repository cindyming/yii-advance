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
                }

            ],
            'updated_at',
            [
                'attribute' => 'status',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('status',true),
            ],
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
                        return Html::a('购买', $url, $options);
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
