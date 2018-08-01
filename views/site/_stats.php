<?php

use app\models\Stats;

Stats::setPrefixies('Velikiy_Novgorod');

?>
<h2 class="h1-responsive font-weight-bold text-center my-5">В нашей базе</h2>
<div class="row text-center">
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <h3>Количество объявлений</h3>
        <canvas id="pieGlobalStats"></canvas>
        <br>
        <table class="table table-sm table-bordered statistic">
            <tr style="background-color: #2ECC71;">
                <td>Реальные</td>
                <td>2700</td>
            </tr>
            <tr style="background-color: #F7464A">
                <td>Дубликаты</td>
                <td>9034</td>
            </tr>
            <tr style="background-color: #85929E">
                <td>Проданные</td>
                <td>300</td>
            </tr>

            <tr style="background-color: deepskyblue">
                <td>Всего</td>
                <td>12345</td>
            </tr>
        </table>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <h3>Собственников/агентов</h3>
        <canvas id="pieOwnersAgents"></canvas>
        <br>
        <table class="table table-sm table-bordered statistic">
            <tr style="background-color: #F7464A"
            ">
            <td>Агенты</td>
            <td>2800</td>
            </tr>
            <tr style="background-color: #2ECC71">
                <td>Собственники</td>
                <td>200</td>
            </tr>

            <tr style="background-color: deepskyblue">
                <td>Всего</td>
                <td>3000</td>
            </tr>

        </table>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-12 col-12">
        <h3>Источники</h3>
        <canvas id="pieSources"></canvas>
        <br>
        <table class="table table-sm table-bordered statistic">
            <tr style="background-color: #A569BD;">
                <td>Avito</td>
                <td>3100</td>
            </tr>
            <tr style="background-color: #5DADE2">
                <td>Cian</td>
                <td>6000</td>
            </tr>
            <tr style="background-color: #E74C3C">
                <td>Irr</td>
                <td>1600</td>
            </tr>
            <tr style="background-color: #FFC870">
                <td>Yandex</td>
                <td>1300</td>
            </tr>

            <tr style="background-color: deepskyblue">
                <td>Всего</td>
                <td>12000</td>
            </tr>
        </table>
    </div>

</div>


<?php
// $saleCountAll = Stats::countSale(['disactive' => \app\models\Sale::ACTIVE]);
// $saleCountSold = Stats::countSale(['status' => \app\models\SaleSimilar::SOLD]);
$script = <<< JS
var pieGlobalStats = document.getElementById("pieGlobalStats").getContext('2d');
var myPieChartpieGlobalStats = new Chart(pieGlobalStats, {
    type: 'pie',
    data: {
        labels: ["Дубликаты", "Реальные", "Проданные"],
        datasets: [
            {
                data: [7000, 2700, 300],
                backgroundColor: ["#F7464A", "#2ECC71", "#85929E"],
               // hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870"]
            }
        ]
    },
    options: {
        responsive: true
    }
});
var pieOwnersAgents = document.getElementById("pieOwnersAgents").getContext('2d');
var myPieChartpieOwnersAgents = new Chart(pieOwnersAgents, {
    type: 'pie',
    data: {
        labels: ["Собственники", "Агенты"],
        datasets: [
            {
                data: [200, 2800],
                backgroundColor: ["#2ECC71","#F7464A"],
               // hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870"]
            }
        ]
    },
    options: {
        responsive: true
    }
});

var pieSources = document.getElementById("pieSources").getContext('2d');
var myPiepieSources = new Chart(pieSources, {
    type: 'pie',
    data: {
        labels: ["Avito", "Cian","Irr", "Yandex"],
        datasets: [
            {
                data: [3100, 6000, 1600, 1300],
                backgroundColor: ["#A569BD","#5DADE2","#E74C3C  ","#FFC870"],
               // hoverBackgroundColor: ["#FF5A5E", "#5AD3D1", "#FFC870"]
            }
        ]
    },
    options: {
        responsive: true
    }
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
