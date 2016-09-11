<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '正式会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="b_download">
        <?= Html::a('下载最近一周', '/member/export?week=1') ?>
        <?= Html::a('下载筛选数据', '/member/export', array('onClick' =>"$(this).attr('href', $(this).attr('href') + window.location.search);", "target"=>'_blank')) ?>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'username',
            'nickname',
            'phone',
            'identity',
            [
                'attribute' =>  'approved_at',
                'filterType'=>GridView::FILTER_DATE_RANGE,
            ],
            [
                'attribute' => 'locked',
                'filter' => Yii::$app->options->getOptions('locked'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('locked', $model->locked);
                }
            ],

            [
                'attribute' => 'country',
                'filter' => Yii::$app->options->getOptions('country'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('country', $model->country);
                }
            ],
            [
                'attribute' => 'buy_stack',
                'filter' => Yii::$app->options->getOptions('buy_stack'),
                'value' => function($model) {
                    return Yii::$app->options->getOptionLabel('buy_stack', $model->buy_stack);
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '修改',
                'hiddenFromExport' => true,
                'template' => Yii::$app->user->identity->isStackAdmin() ? '' : '{update} {resetpassword}',
                'buttons' => [
                    'resetpassword' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '重置密码'),
                            'aria-label' => Yii::t('yii', '重置密码'),
                            'data-confirm' => Yii::t('yii', '你确定要为会员[' . $model->username . ']重置密码?密码将被设置为123456'),
                            'target' => '_blank'
                        ];
                        return Html::a('重置密码', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'resetpassword') {
                        $url = '/member/resetpassword?id='.$model->id;
                        return $url;
                    }
                    if ($action === 'update') {
                        $url = '/member/update?id='.$model->id;
                        return $url;
                    }
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '删除',
                'hiddenFromExport' => true,
                'template' => Yii::$app->user->identity->isStackAdmin() ? '' : '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '删除'),
                            'aria-label' => Yii::t('yii', '删除'),
                            'data-confirm' => Yii::t('yii', '你确定要删除会员[' . $model->username . '],所有的记录都会被清除.'),
                            'target' => '_blank'
                        ];
                        return Html::a('删除', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'delete') {
                        $url = '/member/adelete?id='.$model->id;
                        return $url;
                    }
                }
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '操作',
                'hiddenFromExport' => true,
                'template' => Yii::$app->user->identity->isStackAdmin() ? '' : '{login}',
                'buttons' => [
                    'login' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '登录会员平台'),
                            'aria-label' => Yii::t('yii', '登录会员平台'),
                            'data-pjax' => '0',
                            'target' => '_blank'
                        ];
                        return Html::a('登录会员平台', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'login') {
                        $url = str_replace('admin', 'www', Yii::$app->getRequest()->getHostInfo()) .  '/member/autologin?id='.$model->id;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
