<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Message */

$this->title = 'Message Detail';
?>
<div class="message-view">

    <h1>[<?php echo Yii::$app->options->getOptionLabel('question_type',$model->type) ?>]<?= Html::encode($this->title) ?></h1>

    <div>
         <div>
             留言内容[<?php echo $model->created_at; ?>]
         </div>
        <p>
            <?php echo $model->content; ?>
        </p>
    </div>

    <div>
        <div>
            客服回复[<?php echo $model->updated_at ?>]
        </div>
        <p>
            <?php echo $model->replied_content; ?>
        </p>
    </div>
    <p>
        <?= Html::a('返回', ['index'], ['class' => 'btn btn-primary']) ?>
    </p>

</div>
