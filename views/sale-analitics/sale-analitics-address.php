<?php

use app\models\SaleAnaliticsAddress;
use app\models\Sale;

/* @var $this yii\web\View */
/* @var $SaleAnaliticsAddress app\models\SaleAnaliticsAddress */
/* @var $sale  app\models\Sale */
?>
<? info("СРЕДНЯЯ ЦЕНА ПО АДРЕСУ"); ?>
<?php echo "<br> " . \app\models\Renders::Price($SaleAnaliticsAddress->average_price) . " при количестве вариантов " . $SaleAnaliticsAddress->average_price_count; ?>
<hr>
<?php if ($SaleAnaliticsAddress->average_price != 0) {
    $sales = Sale::find()
        ->where(['in', 'id', explode(',', $SaleAnaliticsAddress->ids_sale_history)])
        ->orderBy('price')
        ->all();

}
?>

<div class="row">
    <div class="col-6">
        <?= $this->render('@app/views/sale/_mini_sale-table', ['sales' => $sales]); ?>
    </div>
    <div class="col-6">
        <?  echo $this->render('@app/views/sale/_yandex-map', ['sales' => $sales, 'id' => 'SaleAnaliticsAddress']); ?>
    </div>
</div>


