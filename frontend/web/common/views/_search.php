<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\MemberSerach */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'auth_key') ?>

    <?= $form->field($model, 'username') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'locked') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <?php // echo $form->field($model, 'role_id') ?>

    <?php // echo $form->field($model, 'nickname') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'password_hash2') ?>

    <?php // echo $form->field($model, 'identity') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'investment') ?>

    <?php // echo $form->field($model, 'bank') ?>

    <?php // echo $form->field($model, 'cardname') ?>

    <?php // echo $form->field($model, 'cardnumber') ?>

    <?php // echo $form->field($model, 'bankaddress') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'qq') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'approved_at') ?>

    <?php // echo $form->field($model, 'buy_stack') ?>

    <?php // echo $form->field($model, 'added_by') ?>

    <?php // echo $form->field($model, 'stack_fund') ?>

    <?php // echo $form->field($model, 'finance_fund') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
