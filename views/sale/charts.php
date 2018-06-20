<?php echo $message;
?>


<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load("current", {packages: ['corechart']});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
        var data = google.visualization.arrayToDataTable([
            ["Element", "Density", {role: "style"}],
            <?
            foreach ($new_prices as $price) {

               echo "[\"е\", {$price}, \"silver\"], ";

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

<?php echo \app\components\SaleTableWidgets::widget([
    'salefilter' => $salefilter,
    'sales' => $sales_history
]); ?>



