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
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Member'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'nickname',
            'investment',
            [
                'attribute' => 'added_by',
                'value' => function($model) {
                    $parent = $model->getAddedByMember()->one();
                    return ($parent) ? $parent->username : '#';
                },
            ],
            'phone',
            'created_at',
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
