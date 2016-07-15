<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Cash */

$this->title = '增减货币';
$isOut = (Yii::$app->request->get('type') == 'out');
?>
<div class="cash-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= Html::a('返回', ['/account/list'])?>

    <ul class="tabswitch">
    <?php if ($isOut): ?>
        <li><?= HTML::a('添入会员账户', ['/account/add?type=in', 'id' => Yii::$app->getRequest()->get('id')])?></li>
        <li class="active">扣出会员账户</li>
    <?php else: ?>
            <li class="active">添入会员账户</li>
            <li><?= HTML::a('扣出会员账户', ['/account/add?type=out', 'id' => Yii::$app->getRequest()->get('id')])?></li>
    <?php endif ?>
    </ul>

    <div class="cash-_form sm-form">

        <?php $form = ActiveForm::begin([
            'action' => ($isOut) ? '/account/add?type=out' : '/account/add?type=in',
            'validateOnBlur' => true,
            'enableAjaxValidation' => true,
            'validationUrl' => '/account/validateadd?type=' . (($isOut) ? 'out' : 'in'),
        ]); ?>
        <?= $form->field($model, 'membername')->textInput(['value' => Yii::$app->getRequest()->get('id')]); ?>
        <?= $form->field($model, 'amount') ?>
        <?= $form->field($model, 'account_type')->radioList(Yii::$app->options->getOptions('account_type')); ?>
        <?= $form->field($model, 'note')->textInput(['value' => (($isOut) ? '扣除金额' :'预存金额')]) ?>
        <div class="form-group">
            <?= Html::submitButton('保存', ['class' => 'btn btn-primary','id' => 'btn']) ?>
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
