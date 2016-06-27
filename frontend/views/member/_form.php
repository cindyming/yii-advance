<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Member */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="member-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validationUrl' => '/member/validate?' . ($model->id ? 'id=' . $model->id : ''),
    ]); ?>
    <?= $form->field($model, 'added_by')->hiddenInput(['maxlength' => true, 'value' => Yii::$app->user->identity->id])->label('') ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'title')->dropDownList(Yii::$app->options->getOptions('title')) ?>


    <?= $form->field($model, 'password', ['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password_confirm',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password2_confirm',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank')->dropDownList(Yii::$app->options->getOptions('bank')) ?>

    <?= $form->field($model, 'cardname')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cardnumber')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankaddress')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'qq')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
