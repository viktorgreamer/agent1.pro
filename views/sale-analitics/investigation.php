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

$this->title = 'Исследование ценообразования';
$this->params['breadcrumbs'][] = $this->title;

// my_var_dump($query);
?>
    <div class="sale-index">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php echo $this->render('@app/views/sale/_search', ['salefilter' => $salefilter]); ?>
        <div class="row">
            <div class="col-12">
                <table class="table-bordered">
                    <thead>
                    <td> параметр</td>
                    <td>Count</td>
                    <td> руб./м2</td>
                    <td> средняя цена</td>
                    </thead>
                    <?php foreach ([1] as $i => $rooms_count) {
                        $export[$i] = [];
                        $cloneQuery = clone $query;
                        $cloneQuery2 = clone $query;
                        // модификафия запроса
                        $cloneQuery->andwhere(['rooms_count' => $rooms_count]);
                        // выборка данных
                        $datas = $cloneQuery->select(['id', 'grossarea', 'price'])->asArray()->all();
                        // my_var_dump($datas);
                        $count = count($datas);
                        // сладем в массив среднее значение
                        foreach ($datas as $data) {
                            if ($data['grossarea']) {
                                $average = round(0.001 * $data['price'] / $data['grossarea'], 1);
                                array_push($export[$i], ['id' => $data['id'], 'average' => $average]);
                            }
                        }
                        //  my_var_dump($export);

                        $export[$i] = MyArrayHelpers::RemoveOver($export[$i], 'average', 0,20);
                        ArrayHelper::multisort($export[$i], 'average', SORT_ASC);
                        // my_var_dump($export[$i]);
                        $count = count($export[$i]);
                        $salesQuery = \app\models\Synchronization::find()
                            ->where(['in', 'id', \yii\helpers\ArrayHelper::getColumn($export[$i], 'id')]);
                        $sales = $cloneQuery2->all();

                        ?>
                        <tr>
                            <td> <?= \app\models\Sale::ROOMS_COUNT_ARRAY[$rooms_count] ?></td>
                            <td> <?= $count ?></td>
                            <td> <? if ($count) echo round(array_sum(ArrayHelper::getColumn($export[$i], 'average')) / $count, 1); ?></td>
                            <td> <?=  \app\models\Renders::Price(round($query->average('price'),-3));  ?></td>
                        </tr>
                        <?php


                    } ?>
                </table>
            </div>
        </div>
        <?php ?>
        <canvas id="lineChart" style="height: 200px"></canvas>
        <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
           Show
        </a>
    <div class="collapse" id="collapseExample">
        <div class="row">
            <div class="col-9">
                <?php
                 echo $this->render('@app/views/sale/_mini_sale-table',['sales'=>$sales]);
                ?>
            </div>
        </div>
    </div>

        <canvas id="chartPrices" style="height: 200px"></canvas>
    </div>

<?php
$salesQuery1 = $cloneQuery2;

$export_data = json_encode(\yii\helpers\ArrayHelper::getColumn($export[0], 'average'));
$export_labels = json_encode(\yii\helpers\ArrayHelper::getColumn($export[0], 'id'));
$export_prices = json_encode($cloneQuery2->select('price')->orderBy('price')->column());
$export_prices_labels = json_encode($salesQuery1->select('id')->orderBy('price')->column());
// my_var_dump($export_data);
$script = <<< JS
var ctxL = document.getElementById("lineChart").getContext('2d');
var myLineChart = new Chart(ctxL, {
    type: 'line',
    data: {
        labels: $export_labels,
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(255,99,132,1)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(255,99,132,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: $export_data
            },
            
        ]
    },
    options: {
       // responsive: true
    }    
});
var ctxL = document.getElementById("chartPrices").getContext('2d');
var chartPrices = new Chart(ctxL, {
    type: 'line',
    data: {
        labels: $export_prices_labels,
        datasets: [
            {
                label: "My First dataset",
                fillColor: "rgba(255,99,132,1)",
                strokeColor: "rgba(220,220,220,1)",
                pointColor: "rgba(255,99,132,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(220,220,220,1)",
                data: $export_prices
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
