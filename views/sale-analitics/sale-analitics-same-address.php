<?php

use app\models\SaleAnaliticsSameAddress;
use app\models\Sale;

/* @var $this yii\web\View */
/* @var $SaleAnaliticsSameAddress app\models\SaleAnaliticsSameAddress */
/* @var $sale  app\models\Sale */
?>
<? info("СРЕДНЯЯ ЦЕНА ПО РАЙОНУ"); ?>
<?php echo "<br> " . \app\models\Renders::Price($SaleAnaliticsSameAddress->average_price) . " при количестве вариантов " . $SaleAnaliticsSameAddress->average_price_count; ?>
<?php echo "<br> В радиусе " . $SaleAnaliticsSameAddress->radius; ?>

    <hr>
    <canvas id="canvas" style="height: 500px"></canvas>
  <?php if ($SaleAnaliticsSameAddress->average_price != 0) {
    $sales = Sale::find()
        ->where(['in', 'id', explode(',', $SaleAnaliticsSameAddress->ids_sale_history)])
        ->orderBy('price')
        ->all();

}
?>

    <div class="row">
        <div class="col-6">
            <?= $this->render('@app/views/sale/_mini_sale-table', ['sales' => $sales]); ?>
        </div>
        <div class="col-6">
            <? echo $this->render('@app/views/sale/_yandex-map', ['sales' => $sales, ['sales' => $sales, 'id' => 'SaleAnaliticsSameAddress']]); ?>

        </div>
    </div>

<?php
$allPrices = $_SESSION['allPrices'];
$trimmedPrices = $_SESSION['trimmedPrices'];
$average_line = [];
$priceMIN_line = [];
$priceMAX_line = [];
foreach ($allPrices as $price) {
    array_push($average_line, $SaleAnaliticsSameAddress->average_price);
    array_push($priceMIN_line, $SaleAnaliticsSameAddress->priceMIN);
    array_push($priceMAX_line, $SaleAnaliticsSameAddress->priceMAX);
}
$average_line = json_encode($average_line);
$priceMIN_line = json_encode($priceMIN_line);
$priceMAX_line = json_encode($priceMAX_line);
$prices = json_encode($allPrices);
$trimmedPrices = json_encode($trimmedPrices);
// my_var_dump($export_data);
$script = <<< JS
var config = {
            type: 'line',
            data: {
                labels: $prices,
                datasets: [{
                    label: "prices",
                    fill: false,
                    backgroundColor: '#0000FF',
                    borderColor: '#0000FF',
                    data: $prices,
                }, {
                    label: "average_line",
                    fill: false,
                    backgroundColor: '#FF0000',
                    borderColor: '#FF0000',
                    borderDash: [5, 5],
                    data: $average_line,
                }, {
                    label: "priceMIN_line",
                    backgroundColor: '#006400',
                    borderColor: '#006400',
                    data: $priceMIN_line,
                    fill: false,
                }, {
                    label: "priceMAX_line",
                    backgroundColor: '#006400',
                    borderColor: '#006400',
                    data: $priceMAX_line,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                title:{
                    display:true,
                    text:'Chart.js Line Chart'
                },
                tooltips: {
                    mode: 'index',
                    intersect: false,
                },
                hover: {
                    mode: 'nearest',
                    intersect: true
                },
                scales: {
                    xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Month'
                        }
                    }],
                    yAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Value'
                        }
                    }]
                }
            }
        };

window.onload = function() {
    var ctx = document.getElementById("canvas").getContext("2d");
    window.myLine = new Chart(ctx, config);
};
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);
