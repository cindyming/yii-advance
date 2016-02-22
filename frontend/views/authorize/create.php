<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StackAuthorize */

$this->title = Yii::t('app', 'Create Stack Buy Authorize');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stack Authorizes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-authorize-create sm-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'stack_code' => $stack_code
    ]) ?>

</div>
