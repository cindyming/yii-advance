<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = 'Message Detail';
?>
<div class="message-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'title',
            'content:ntext',
            [
                'attribute' => 'replied_content',
                'content' => function($model) {
                    if ($model->replied_content) {
                        return $model->replied_content;
                    } else {
                        return '';
                    }
                }
            ]
        ],
    ]) ?>

    <p>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
