<?php

use yii\helpers\Html;
use kartik\grid\GridView;;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stacks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stack-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <p>
        <?= Html::a(Yii::t('app', 'Create Stack'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="stack_list">
        <?php foreach ($stacks as $stack): ?>
            <div class="item <?= $stack->status ?  'disabled' : 'enabled' ?>">
                <div class="name" ><?= $stack->name?></div>
                <div class="stackPrice" id="price<?= $stack->id?>"><?= $stack->price?></div>
                <div class="updatePrice">
                    <?= Html::input('text', 'price', '', ['id' => "priceNew" . $stack->id]) ?>
                    <?= Html::button('改价', ['class' => 'btn btn-primary', 'onclick' => "changePrice(" .$stack->id. ")"]) ?>
                </div>
                <div class="time" ><label id="update<?= $stack->id?>"><?= $stack->updated_at?> </label>更新</div>
            </div>
        <?php endforeach ?>

    </div>
</div>
<script type="text/javascript">
    function changePrice(id) {
        var price = $('#priceNew' + id).val();
        if (price) {
            $('#priceNew' + id).parents('div.updatePrice').removeClass('has-error');
            $.post('/stack/changeprice?id=' + id, {'price':price}, function(data){
                if (data.status) {
                    $('#price' + id).html(price);
                    $('#update' + id).html(data.update);
                    $('#priceNew' + id).val('');
                } else {
                    alert(data.message);
                }
            }, 'json');
        } else {
            $('#priceNew' + id).parents('div.updatePrice').addClass('has-error');
            alert('请先输入新价格')
        }

    }
</script>
