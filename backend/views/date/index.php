<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\DateSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Dates');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="date-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'date',
            [
                'attribute'=>'status',
                'value' => function($model){
                    return Yii::$app->options->getOptionLabel('date_status', $model->status);
                },
                'filter' => Yii::$app->options->getOptions('date_status'),

            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '修改',
                'template' => '{change}',
                'buttons' => [
                    'change' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '改变状态'),
                            'aria-label' => Yii::t('yii', '改变状态'),
                        ];
                        return  Html::a('改变状态', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'change') {
                        $url ='/date/update?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
