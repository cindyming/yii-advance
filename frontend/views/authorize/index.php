<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackAuthorizeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stack Authorizes');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-authorize-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Stack Buy Authorize'), ['create'], ['class' => 'btn btn-success']) ?>

        <?= Html::a(Yii::t('app', 'Create Stack Sell Authorize'), ['sell'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'membername',
                'value' => function($model) {
                    return $model->member->username;
                }
            ],
            [
                'attribute' => 'stackcode',
                'value' => function($model) {
                    return $model->stack->code;
                }
            ],
            [
                'attribute' => 'type',
                'filter' => Yii::$app->options->getOptions('authorize_type'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('authorize_type', $model->type);
                }
            ],
            [
                'attribute' => 'volume',
                'filter' => false,
            ],
            [
                'attribute' => 'price',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'real_price',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'status',
                'filter' => Yii::$app->options->getOptions('authorize_status'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('authorize_status', $model->status);
                }
            ],
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'id',
                'label' => '操作',
                'filter' => false,
                'hiddenFromExport' => true,
                'content' => function($model) {
                    return ($model->status == 1) ? ( Html::a('取消', '/authroize/delete?id='.$model->id, ['data-confirm'=>"你确定要取消当前委托"])) : '';
                }
            ],
        ],
    ]); ?>

</div>
