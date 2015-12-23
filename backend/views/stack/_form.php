<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Stack */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stack-form">

    <?php $form = ActiveForm::begin(); ?>


    <?= $form->field($model, 'code')->textInput(['maxlength' => true, 'readOnly' => ($model->isNewRecord ? false : true)]) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'status')->dropDownList(Yii::$app->options->getOptions('status')) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
