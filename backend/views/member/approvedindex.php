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

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'export'=>[
            'fontAwesome'=>true,
            'showConfirmAlert'=>false,
            'target'=>GridView::TARGET_BLANK
        ],
        'exportConfig' => [
            GridView::EXCEL => ['label' => '保存为Excel文件']
        ],
        'toolbar'=>[
            '{export}',
            '{toggleData}'
        ],
        'panel'=>[
            'type'=>GridView::TYPE_PRIMARY,
        ],
        'autoXlFormat' => true,
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
                'template' => '{update} {resetpassword}',
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
                'header' => '操作',
                'hiddenFromExport' => true,
                'template' => '{login}',
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
