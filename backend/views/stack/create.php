<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Stack */

$this->title = Yii::t('app', 'Create Stack');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
