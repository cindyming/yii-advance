<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '全部会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'nickname',
            'identity',
            [
                'attribute' => 'bank',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('bank', $model->bank);
                },
                'filter' => Yii::$app->options->getOptions('bank',true),
            ],
            'cardname',
            'cardnumber',
            'bankaddress',
            'phone',
            [
                'attribute' => 'role_id',
                'label' => '状态',
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('role', $model->role_id);
                },
                'filter' => Yii::$app->options->getOptions('role',true),
            ],
        ],
    ]); ?>

</div>
