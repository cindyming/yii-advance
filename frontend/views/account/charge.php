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
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'btn']) ?>
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
</div><!-- cash-_form -->

</div>