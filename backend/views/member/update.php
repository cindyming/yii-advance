<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Member */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Member',
]) . ' ' . $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Members'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="member-update">

    <h1><?= Html::encode($this->title) ?> (投资额: <?= $model->investment  ?>)</h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
