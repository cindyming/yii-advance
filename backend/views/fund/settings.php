<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\System */

$this->title = Yii::t('app', 'Fund Settings')
?>
<div class="system-create">
	<h1><?= Html::encode($this->title) ?></h1>
    <div class="system-form sm-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'name')->textInput() ?>
        <?= $form->field($model, 'daily')->textInput() ?>
        <?= $form->field($model, 'monthly')->textInput() ?>
        <?= $form->field($model, 'excepted')->textInput() ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? '保存' : '保存', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
'
        <?php ActiveForm::end(); ?>

    </div>

</div>
