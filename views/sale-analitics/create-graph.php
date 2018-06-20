<?php
use yii\widgets\ActiveForm;
use yii\bootstrap\ActiveField;
use yii\helpers\Html;
use app\components\Mdb;

?>



<?= $message ?>
<form class="row" method="get" action="create-graph">
    <div class="col-2">
        <?php echo Mdb::ActiveSelect($filter, 'rooms_count', \app\models\Sale::ROOMS_COUNT_ARRAY); ?>
    </div>
    <div class="col-2">
        <?php echo Mdb::ActiveTextInput($filter, 'S_min', []); ?>
    </div>
    <div class="col-2">
        <?php echo Mdb::ActiveTextInput($filter, 'S_max', []); ?>
    </div>
    <div class="col-2">
        <?php echo Mdb::ActiveSelect($filter, 'floorcount', \app\models\Sale::getFloors()); ?>
    </div>
    <div class="col-2">
        <?php echo Mdb::ActiveSelect($filter, 'house_type', \app\models\Sale::HOUSE_TYPES); ?>
    </div>
    <div class="col-2">
        <?= Html::submitButton('Выполнить', ['class' => 'btn btn-primary']); ?>
    </div>
</form>

<?php

?>
<?php if ((count($data) != 0) and ($s_min != $s_max) and (max($data) != 0)) {
    $avg = round(array_sum($data) / count($data));
    echo "Среднее значение массива" . $avg;
    echo "<br> количество данных ".count($ss);
}
$google_datas = [['S',' COUNT']];
foreach ($data as $key=>$item) {
    array_push($google_datas,[(string) $ss[$key], $data[$key]]);
}
$google_datas = json_encode($google_datas);
$data = json_encode($data);
$avarage_price = json_encode($avarage_price);
$ss = json_encode($ss);

// echo $data;
// echo $ss;
?>


<!--<canvas id="canvas" style="height: 500px"></canvas>-->
<div id="curve_chart" style="height: 400px"></div>
<?php
  $this->registerMetaTag(['http-equiv' => 'Cache-control', 'content' => 'no-cache']);
$script = <<< JS
var config = {
            type: 'line',
            data: {
                labels: $ss,
                datasets: [{
                    label: "prices",
                    fill: false,
                    backgroundColor: '#0000FF',
                    borderColor: '#0000FF',
                    data: $data,
                } ]
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
 // $this->registerJs($script, yii\web\View::POS_READY);

$script = <<< JS
google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable($google_datas);

        var options = {
          title: 'Company Performance',
          curveType: 'function',
          legend: { position: 'bottom' },
          chartArea:{left:30,top:30,width:"80%",height:"80%"}
   
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));

        chart.draw(data, options);
      }
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
