<?php

use app\models\SaleAnalitics;
use app\models\Sale;

/* @var $this yii\web\View */
/* @var $SaleAnalitics app\models\SaleAnalitics */
/* @var $sale  app\models\Sale */
?>
<? info("ОБЩАЯ ЦЕНА ПО ГОРОДУ"); ?>
<?php echo "<br> Средняя цена по городу" . \app\models\Renders::Price($SaleAnalitics->average_price) . " при количестве вариантов " . $SaleAnalitics->average_price_count; ?>
<hr>


<?php if ($SaleAnalitics->average_price != 0) {
    $sales = Sale::find()
        ->where(['in', 'id', explode(',', $SaleAnalitics->ids_sale_history)])
        ->orderBy('price')
        ->all();

}
?>

<div class="row">
    <div class="col-6">
        <?=  $this->render('@app/views/sale/_mini_sale-table', ['sales' => $sales]); ?>
    </div>
    <div class="col-6">
        <?  echo $this->render('@app/views/sale/_yandex-map', ['sales' => $sales, 'id' => 'SaleAnalitics']); ?>
    </div>
</div>
