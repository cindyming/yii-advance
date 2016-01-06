<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Stack */

$this->title = Yii::t('app', 'Create Stack Fund');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-create sm-form">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_buyform', [
        'model' => $model,
    ]) ?>

</div>
