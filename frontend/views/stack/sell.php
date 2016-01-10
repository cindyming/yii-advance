<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Stack */
/* @var $form yii\widgets\ActiveForm */
$this->title = Yii::t('app', 'Sell Stack');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-create">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="stack-form">

        <?php $form = ActiveForm::begin([
//            'enableAjaxValidation' => true,
//            'validateOnBlur' => true,
//            'validationUrl' => '/account/validatebuy?' . ($model->id ? 'id=' . $model->id : ''),
        ]); ?>
        <?= $form->field($model, 'account_type')->label('')->hiddenInput() ?>
        <?= $form->field($model, 'stackname')->textInput(['maxlength' => true, 'value' => $stack->name ,'readOnly' => ( true)]) ?>
        <?= $form->field($model, 'stackcode')->textInput(['maxlength' => true, 'value' => $stack->code ,'readOnly' => ( true)]) ?>
        <?= $form->field($model, 'stackprice')->textInput(['maxlength' => true, 'value' => $stack->price ,'readOnly' => ( true)]) ?>
        <?= $form->field($model, 'sellnumber')->textInput(['maxlength' => true, 'value' => $memberStack->sell_volume ? $memberStack->sell_volume : 0 ,'readOnly' => ( true)]) ?>
        <?= $form->field($model, 'locknumber')->textInput(['maxlength' => true, 'value' => $memberStack->lock_volume ? $memberStack->lock_volume : 0 ,'readOnly' => ( true)]) ?>

        <?= $form->field($model, 'volume')->textInput(['maxlength' => true])->label(Yii::t('app', 'Exchange Volume')) ?>
        <?= $form->field($model, 'total_price')->textInput(['maxlength' => true, 'value' => $model->total_price ,'readOnly' => ( true)]) ?>
        <?= Html::submitButton('计算总价', ['class' => 'btn btn-primary', 'onClick' => "$('#stacktransaction-account_type').val(0)"]) ?>
        <?= $form->field($model, 'password2',['options' => ['class' => 'form-group required']])->passwordInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Sell Stack') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'onClick' => "$('#stacktransaction-account_type').val(1)"]) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
