<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '僵尸会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="margin-bottom: 10px;"> <button onclick="deleteAll()">废除选中的会员账号</button></div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items} {summary} {pager}',
        'pjax' => true,
        'id' => 'Cancel',
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'username',
                'content' => function ($model) { return $model->id;}

            ],
            [
                'attribute' => 'id',
                'hidden' => true,
            ],
            [
                'attribute' => 'username',
                'header' => '会员编号',
            ],
            [
                'attribute' => 'nickname',
                'header' => '会员昵称',
            ],


            [
                'attribute' => 'itotal',
                'header' => '资金数',
            ],
            [
                'attribute' => 'total',
                'header' => '总股数',
            ],



            [
                'attribute' => 'identity',
                'header' => '证件号',
            ],
            [
                'attribute' => 'cardname',
                'header' => '开户名',
            ],
            [
                'attribute' => 'cardnumber',
                'header' => '银行卡号',
            ],
            [
                'attribute' => 'bankaddress',
                'header' => '开户行',
            ],
            [
                'attribute' => 'phone',
                'header' => '电话号码',
            ]
        ],
    ]); ?>

</div>
<script type="text/javascript">
    function deleteAll() {

        result = confirm('所有选中的用户将被废除');

        if(result) {
            var keys = $('#Cancel').yiiGridView('getSelectedRows');
            var trs = $('#Cancel table tr td.kv-grid-hide');
            var ids = '';
            $(keys).each(function (i, e) {
                ids +=($(trs[e]).text()) + '-';
            });
            window.location.href = '/member/removeall?id=' + ids;
        }

    }
</script>
