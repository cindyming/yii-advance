<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="member-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'auth_key',
            'username',
            'status',
            'locked',
            'access_token',
            'role_id',
            'nickname',
            'password_hash',
            'password_hash2',
            'identity',
            'phone',
            'title',
            'investment',
            'bank',
            'cardname',
            'cardnumber',
            'bankaddress',
            'email:email',
            'qq',
            'created_at',
            'updated_at',
            'approved_at',
            'buy_stack',
            'added_by',
            'stack_fund',
            'finance_fund',
        ],
    ]) ?>

</div>
