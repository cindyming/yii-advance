<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Cash */

$this->title = '申请提现';
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>
<div class="cash-_form sm-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bank')->dropDownList(Yii::$app->options->getOptions('bank'), ['value' => Yii::$app->user->identity->bank, 'readonly' => true]) ?>
    <?= $form->field($model, 'cardnumber')->textInput(['value' => Yii::$app->user->identity->cardnumber, 'readonly' => true ]); ?>
    <?= $form->field($model, 'cardname')->textInput(['value' => Yii::$app->user->identity->cardname, 'readonly' => true]); ?>
    <?= $form->field($model, 'backaddress')->textInput(['value' => Yii::$app->user->identity->bankaddress, 'readonly' => true]); ?>
    <?= $form->field($model, 'amount') ?>
    <?= $form->field($model, 'password2',['options' => ['class' => 'form-group required']])->passwordInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
    <?= $form->field($model, 'member_id')->label('')->hiddenInput(['value' => Yii::$app->user->identity->id]) ?>
    <?php ActiveForm::end(); ?>
</div><!-- cash-_form -->

</div>