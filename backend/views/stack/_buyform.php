<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Stack */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="stack-form">

    <?php $form = ActiveForm::begin([
        'enableAjaxValidation' => true,
        'validateOnBlur' => true,
        'validationUrl' => '/stack/validatebuy?' . ($model->id ? 'id=' . $model->id : ''),
    ]); ?>

    <?= $form->field($model, 'membername')->textInput(['maxlength' => true, 'readOnly' => ($model->isNewRecord ? false : true)]) ?>

    <?= $form->field($model, 'stackcode')->dropDownList(\common\models\Stack::getStackOptions()) ?>

    <?= $form->field($model, 'price')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'volume')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btn' ]) ?>
    </div>

    <?php ActiveForm::end(); ?>
<script type="text/javascript"> 
var wait=5; 
function time(o) { 
        if (wait == 0) { 
            o.removeAttribute("disabled");           
            o.value="提交"; 
            wait = 10; 
        } else { 
            o.setAttribute("disabled", true); 
            o.value=wait+"提交中"; 
            wait--; 
            setTimeout(function() { 
                time(o) 
            }, 
            1000) 
        } 
    } 
document.getElementById("btn").onclick=function(){time(this);} 
</script> 
</div>
