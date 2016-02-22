<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StackAuthorize */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stack-authorize-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'account_type')->label('')->hiddenInput() ?>

    <?= $form->field($model, 'stackcode')->dropDownList(\common\models\Stack::getStackOptions(), array('options' => array($stack_code => array('selected' => 'selected')))) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton('确认[理财账户]购买', ['class' => 'btn btn-primary', 'onClick' => "$('#stackauthorize-account_type').val(1)"]) ?>
        <?= Html::submitButton('确认[购股账户]购买', ['class' => 'btn btn-primary', 'onClick' => "$('#stackauthorize-account_type').val(2)"]) ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
