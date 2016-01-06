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

    <?php if($model->isNewRecord):?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
    <?php endif ?>
    
    <?= $form->field($model, 'title')->dropDownList(Yii::$app->options->getOptions('title')) ?>

    <?= $form->field($model, 'nickname')->textInput(['maxlength' => true]) ?>

    <?php if($model->isNewRecord):?>
    <?= $form->field($model, 'password',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password_confirm',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'password2',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'password2_confirm',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>
    <?php else: ?>
        <?= $form->field($model, 'buy_stack')->dropDownList(Yii::$app->options->getOptions('buy_stack')) ?>
        <?= $form->field($model, 'locked')->dropDownList(Yii::$app->options->getOptions('locked')) ?>
    <?php endif ?>

    <?= $form->field($model, 'identity')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?php if($model->isNewRecord):?>
    <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>
    <?php endif ?>

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
