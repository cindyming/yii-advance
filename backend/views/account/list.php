<?php

use yii\helpers\Html;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '账户管理';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Yii::$app->user->identity->isStackTwoAdmin() ? '' :HTML::a('增减货币', ['/account/add?type=in'],['class' => 'btn btn-success'])?>
    </p>
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
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'header' => '序号'
            ],
            'username',
            [
                'attribute' => 'investment',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'finance_fund',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'attribute' => 'stack_fund',
                'format' => 'decimal',
                'filter' => false,
            ],
            [
                'class' => 'kartik\grid\ActionColumn',
                'header' => '操作',
                'template' => '{add}',
                'buttons' => [
                    'add' => function ($url, $model, $key) {
                        $options = [
                            'title' => Yii::t('yii', '增减货币'),
                            'aria-label' => Yii::t('yii', '增减货币'),
                            'data-ajax' => 0
                        ];
                        return Yii::$app->user->identity->isStackTwoAdmin() ? '' : Html::a('增减货币', $url, $options);
                    },
                ],
                'urlCreator' => function ($action, $model, $key, $index) {
                    if ($action === 'add') {
                        $url ='/account/add?type=in&id='.$model->username;
                        return $url;
                    }
                }
            ],
        ],
    ]); ?>

</div>
