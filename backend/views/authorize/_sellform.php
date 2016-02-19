<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\StackAuthorize */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stack-authorize-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'membername')->textInput(['maxlength' => true, 'readOnly' => ($model->isNewRecord ? false : true)]) ?>

    <?= $form->field($model, 'stackcode')->dropDownList(\common\models\Stack::getStackOptions()) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>
    <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
