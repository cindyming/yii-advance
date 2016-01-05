<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;



/* @var $this yii\web\View */
/* @var $model backend\models\User */

$this->title = '密码修改';
?>
<div class="user-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="user-form">

        <div id="changeFirstPassword" class="two-cols">

            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'username_old')->textInput(['minlength' => 6, 'readonly' => true, 'value' => $model->username]) ?>

            <?= $form->field($model, 'password_old')->passwordInput(['minlength' => 6, 'label' => '原一级密码']) ?>

            <?= $form->field($model, 'username')->textInput(['minlength' => 6, 'value' => $model->username])->label(Yii::t('app', 'User Name'))?>

            <?= $form->field($model, 'password')->passwordInput(['minlength' => 6, 'label' => '新一级密码', 'value' => '']) ?>

            <?= $form->field($model, 'password_confirm')->passwordInput(['minlength' => 6, 'label' => '确认新一级密码', 'value' => ''])  ?>
            <div class="form-group">
                <?= Html::submitButton('确认修改', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>


</div>
