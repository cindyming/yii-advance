<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;



/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = '修改密码';
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <div id="changeFirstPassword" class="two-cols">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password_old', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '原一级密码', 'value' => '', 'required' => true])->label('原一级密码') ?>

            <?= $form->field($model, 'password', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '新一级密码', 'value' => '', 'required' => true])->label('新一级密码') ?>

            <?= $form->field($model, 'password_confirm', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '确认新一级密码', 'required' => true])->label('确认新一级密码') ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>


        <div id="changeSecondPassword" class="two-cols">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'password2_old', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '原二级密码', 'value' => '', 'required' => true])->label('原二级密码') ?>

            <?= $form->field($model, 'password2', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '新二级密码', 'value' => '', 'required' => true])->label('新二级密码') ?>

            <?= $form->field($model, 'password2_confirm', ['enableClientValidation' => true])->passwordInput(['maxlength' => true, 'label' => '确认新二级密码', 'required' => true])->label('确认新二级密码') ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>
