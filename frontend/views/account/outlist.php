<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Out Records');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'member.username',
            [
                'attribute' => 'type',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('out_type', $model->type);
                },
                'filter' => Yii::$app->options->getOptions('out_type',true),
            ],
            [
                'attribute' => 'account_type',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('account_type', $model->account_type);
                },
                'filter' => Yii::$app->options->getOptions('account_type',true),
            ],
            'amount',
            'fee',
            'total',
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            'note',
        ],
    ]); ?>

</div>
