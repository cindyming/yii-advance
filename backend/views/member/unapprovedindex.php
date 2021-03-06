<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '待审核会员';
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
            [
                'attribute' => 'investment',
                'filter' => false,
            ],
            [
                'attribute' => 'added_by',
                'value' => function($model) {
                    $parent = $model->getAddedByMember() ? $model->getAddedByMember()->one() : null;
                    return ($parent) ? $parent->username : '#';
                },
            ],
            'phone',
            [
                'attribute' =>  'created_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '拒绝',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '拒绝'),
                            'aria-label' => Yii::t('yii', '拒绝'),
                            'data-confirm' => Yii::t('yii', '你确定要拒绝会员[' . $model->username . ']吗?'),
                            'data-method' => 'post',
                        ];
                        return Html::a('拒绝', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        $url ='/member/reject?id='.$model->id;
                        return $url;
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '删除',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '删除'),
                            'aria-label' => Yii::t('yii', '删除'),
                            'data-confirm' => Yii::t('yii', '你确定要删除会员[' . $model->username . ']吗?'),
                            'data-method' => 'post',
                        ];
                        return Html::a('删除', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        $url ='/member/delete?id='.$model->id;
                        return $url;
                    }
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => '审核',
                'template' => '{approve}',
                'buttons' => [
                    'approve' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '审核'),
                            'aria-label' => Yii::t('yii', '审核'),
                            'data-confirm' => Yii::t('yii', '你确定要审核会员[' . $model->username . ']吗?'),
                            'data-method' => 'post',
                        ];
                        return Html::a('审核', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'approve') {
                        $url ='/member/approve?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
