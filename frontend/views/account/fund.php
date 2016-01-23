<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Funds History');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="fund-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'membername',
                'label' => '编号',
                'value' => function($model) {
                    return $model->getFund()->one()->code;
                }
            ],
            [
                'attribute' => 'id',
                'label' => '基金名称',
                'filter' => false,
                'value' => function($model) {
                    return $model->getFund()->one()->name;
                }
            ],
            [
                'attribute' => 'id',
                'label' => '今日收益率',
                'filter' => false,
                'value' => function($model) {
                    return $model->getFund()->one()->daily;
                }
            ],
            [
                'attribute' => 'id',
                'label' => '月收益率',
                'filter' => false,
                'value' => function($model) {
                    return $model->getFund()->one()->monthly;
                }
            ],
            [
                'attribute' => 'id',
                'label' => '预期收益率',
                'filter' => false,
                'value' => function($model) {
                    return $model->getFund()->one()->excepted;
                }
            ],
            [
                'attribute' => 'investment',
                'filter' => false,
            ],
            [
                'attribute' => 'revenue',
                'filter' => false,
            ],
            [
                'attribute' => 'created_at',
                'filter' => false
            ],
            [
                'attribute' => 'cleared_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'cleared',
                'label' => '状态',
                'value' => function($model) {
                    return $model->cleared ? '清仓' : '持有';
                }
            ],
        ],
    ]); ?>

</div>
