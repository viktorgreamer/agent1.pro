<?php
?>
<?php
$session = Yii::$app->session;
$session->setFlash('Sale', $sales);
$SaleAnaliticsSameAddress = $session->getFlash('SaleAnaliticsSameAddress');
//echo "<br> percent" . $SaleAnaliticsSameAddress->percent . " period" . $SaleAnaliticsSameAddress->period . " years" . $SaleAnaliticsSameAddress->years;
//echo "<br> количенство объектов" . $SaleAnaliticsSameAddress->average_price_count . " при радиусе" . $SaleAnaliticsSameAddress->radius;



if (count($sales) == 1 ) {
    echo \app\components\OneSaleWidget::widget([
        'sales' => $sales
    ]);
}
else echo \app\components\SaleTableWidgets::widget([
    'salefilter' => $salefilter,
    'sales' => $sales
]); ?>

<? if ($sales_history) { ?>
<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Element", "Density", {role: "style"}],
            <?
            foreach ($sales_history as $sale_history) {

               echo "[\"$sale_history->rooms_count к $sale_history->address \", {$sale_history->price}, \"silver\"], ";

            }
            ?>

//            ["Silver", 10.49, "silver"],
             ["real average",<?= $average ?> , "gold"],
              ["Simple_average",<?= $simple_average_price ?> , "red"],
              ["Sliced_acerage",<?= $sliced_average_price ?> , "red"],
//            ["Platinum", 21.45, "color: #e5e4e2"]
        ]);

        var view = new google.visualization.DataView(data);
        view.setColumns([0, 1,
            {
                calc: "stringify",
                sourceColumn: 1,
                type: "string",
                role: "annotation"
            },
            2]);

        var options = {
            title: "Полная таблица всех вариантов",
            width: 1200,
            height: 500,
            bar: {groupWidth: "95%"},
            legend: {position: "none"},
        };
        var chart = new google.visualization.ColumnChart(document.getElementById("columnchart_values"));
        chart.draw(view, options);


    }
</script>
<div id="columnchart_values" style="width: 1400px; height: 600px;"></div>
<?php } ?>
<?php echo \app\components\SaleTableWidgets::widget([
    'salefilter' => $salefilter,
    'sales' => $sales_history
]); ?>



