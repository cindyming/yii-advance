<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\System */
$this->title = '系统设置';
?>
<div class="system-create">
	<h1><?= Html::encode($this->title) ?></h1>
    <div class="system-form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'enable_memmber_login')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'show_add_member')->dropDownList([1 => '开放', 0 => '关闭']) ?>
        <?= $form->field($model, 'maintenance')->dropDownList([1 => '否', 0 => '是']) ?>
        <?= $form->field($model, 'lowest_cash_amount',[ 'template' => "{label}\n{input}百，如您输入1就代表1百\n{hint}\n{error}"])->textInput(['value' => ($model->lowest_cash_amount / 100)]) ?>
        <?= $form->field($model, 'cash_factorage')->textInput() ?>
        <?= $form->field($model, 'sell_fee_rate')->textInput() ?>
        <?= $form->field($model, 'annual_fee')->textInput() ?>
        <?= $form->field($model, 'transaction_rule')->textInput()->label('资金到账日') ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
