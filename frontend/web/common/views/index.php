<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\MemberSerach */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Members');
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
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'auth_key',
            'username',
            'status',
            'locked',
            // 'access_token',
            // 'role_id',
            // 'nickname',
            // 'password_hash',
            // 'password_hash2',
            // 'identity',
            // 'phone',
            // 'title',
            // 'investment',
            // 'bank',
            // 'cardname',
            // 'cardnumber',
            // 'bankaddress',
            // 'email:email',
            // 'qq',
            // 'created_at',
            // 'updated_at',
            // 'approved_at',
            // 'buy_stack',
            // 'added_by',
            // 'stack_fund',
            // 'finance_fund',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
