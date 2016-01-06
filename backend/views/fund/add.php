<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Stack */

$this->title = Yii::t('app', 'Create Fund History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="stack-form sm-form">

        <?php $form = ActiveForm::begin([
            'enableAjaxValidation' => true,
            'validateOnBlur' => true,
            'validationUrl' => '/fund/validateadd?' . ($model->id ? 'id=' . $model->id : ''),
        ]); ?>

        <?= $form->field($model, 'membername')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'investment')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>
