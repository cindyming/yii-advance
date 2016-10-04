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
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '恢复',
                'template' => '{recovery}',
                'buttons' => [
                    'recovery' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '恢复'),
                            'aria-label' => Yii::t('yii', '恢复'),
                            'data-confirm' => Yii::t('yii', '你确定要恢复会员[' . $model->username . ']吗?'),
                            'data-method' => 'post',
                        ];
                        return ($model->role_id == 4 ) ? Html::a('恢复', $url, $options) : '';
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'recovery') {
                        $url ='/member/recovery?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
