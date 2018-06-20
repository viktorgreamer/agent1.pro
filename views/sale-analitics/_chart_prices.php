<?php

use yii\helpers\Html;
use yii\widgets\ListView;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\models\MyArrayHelpers;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleSearch */
/* @var $salefilter app\models\SaleFilters */
/* @var $query \app\models\SaleQuery */
?>
    <canvas id="chartPrices" style="height: 200px"></canvas>
<?php

$prices = json_encode($prices);
$labels = json_encode($labels);
// my_var_dump($export_data);
$script = <<< JS

var ctxL = document.getElementById("chartPrices").getContext('2d');
var chartPrices = new Chart(ctxL, {
    type: 'line',
    data: {
        labels: $labels,
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(255,99,132,1)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(255,99,132,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: $prices
            },
            
        ]
    },
    options: {
       // responsive: true
    }    
});
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);
