<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '会员';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="margin-bottom: 10px;"> <button onclick="deleteAll()">删除选中的会员</button></div>

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
            'username',
            'nickname',
            'identity',
            'cardname',
            'cardnumber',
            'bankaddress',
            'phone',
        ],
    ]); ?>

</div>
<script type="text/javascript">
    function deleteAll() {

        result = confirm('所有选中的用户将被删除');

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
