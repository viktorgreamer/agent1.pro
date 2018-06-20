<?php


use app\models\Sale;

/* @var $this yii\web\View */
/* @var $SaleAnalitics app\models\SaleAnalitics */
/* @var $SaleAnaliticsAddress app\models\SaleAnaliticsAddress */
/* @var $SaleAnaliticsSameAddress app\models\SaleAnaliticsSameAddress */
/* @var $sale  app\models\Sale */


?>


<div class="sale-index">
    <?php echo $this->render('@app/views/sale/_sale-table', ['model' => $sale]); ?>
    <!-- Nav tabs -->
    <ul class="nav nav-tabs nav-justified">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#panel2" role="tab">SaleAnaliticsSameAddress</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#panel1" role="tab">SaleAnalitics</a>
        </li>

        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#panel3" role="tab">SaleAnaliticsAddress</a>
        </li>
    </ul>
    <!-- Tab panels -->
    <div class="tab-content card">
        <div class="tab-pane fade in show active" id="panel2" role="tabpanel">
            <br>
            <?php echo $this->render('sale-analitics-same-address', ['SaleAnaliticsSameAddress' => $SaleAnaliticsSameAddress]); ?>
        </div>
        <!--Panel 1-->
        <div class="tab-pane fade" id="panel1" role="tabpanel">
            <br>

            <?php echo $this->render('sale-analitics', ['SaleAnalitics' => $SaleAnalitics]); ?>
        </div>
        <!--/.Panel 1-->
        <!--Panel 2-->

        <!--/.Panel 2-->
        <!--Panel 3-->
        <div class="tab-pane fade" id="panel3" role="tabpanel">
            <br>
            <?php echo $this->render('sale-analitics-address', ['SaleAnaliticsAddress' => $SaleAnaliticsAddress]); ?>
        </div>
        <!--/.Panel 3-->
    </div>


</div>
