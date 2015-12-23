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
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'membername',
                'value' => function($model) {
                    return $model->getMember()->one()->username;
                }
            ],
            'investment',
            'revenue',
            'created_at',
            'cleared_at',
            [
                'attribute' => 'locked',
                'label' => '锁定',
                'hiddenFromExport' => true,
                'filter' => Yii::$app->options->getOptions('locked'),
                'content' => function($model) {
                    return ($model->locked) ? '已锁定' :( Html::a('现在锁定', '/fund/lock?id='.$model->id, ['data-confirm'=>"你确定要锁定吗?"]));
                }
            ],
            [
                'attribute' => 'cleared',
                'label' => '清仓',
                'hiddenFromExport' => true,
                'filter' => Yii::$app->options->getOptions('cleared'),
                'content' => function($model) {
                    return ($model->cleared) ? '已清仓' :( Html::a('现在清仓', '/fund/clear?id='.$model->id, ['data-confirm'=>"你确定要清仓吗?"]));
                }
            ],
        ],
    ]); ?>

</div>
