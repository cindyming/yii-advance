<?php

use yii\helpers\Html;

$this->title = Yii::t('app', 'Stacks List');
?>

<?php
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StackSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->params['breadcrumbs'][] = $this->title;

?>
<div class="stack-index">

    <h1><?= yii\helpers\Html::encode($this->title) ?></h1>

    <div class="stack_list">
        <?php foreach ($stacks as $stack): ?>
            <div class="item <?= $stack->status ?  'disabled' : 'enabled' ?>">
                <div class="name" ><?= $stack->name?></div>
                <div class="stackPrice" id="price<?= $stack->id?>"><?= $stack->price?></div>
                <div class="buy"><?php echo Html::a('购买', '/stack/buy?id=' . $stack->id)?></div>
                <div class="time" ><label id="update<?= $stack->id?>"><?= $stack->updated_at?> </label>更新</div>
            </div>
        <?php endforeach ?>
    </div>

</div>


<script type="text/javascript">
    function refreshPrice()
    {
        $.ajax({
            url:"/stack/prices",
            async:false,
            dataType:'json',
            success: function(result) {
                $('.stackPrice').each(function(i, e){
                    key = $(e).attr('id');
                    $(e).html(result[key]['price']);
                    $('#' + key.replace('price', 'update')).html(result[key]['update']);
                });
                setTimeout('refreshPrice()',1000);
            }
        });
    }

    setTimeout('refreshPrice()',1000);
</script>
