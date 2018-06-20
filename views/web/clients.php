<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SaleLists;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;
use app\components\SaleFilterWidgets;
$no_Clients = " К сожалению, у нас пока нет клиентов на вашу квартиру";
echo $this->render('_counters');
?>
<div class="row">

    <div class="col-md-9 ">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs nav-justified">
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#panel1" role="tab">на 1ккв.<br>
                    (<?= count($clients_1); ?> шт.)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#panel2" role="tab">на 2ккв. <br>(<?= count($clients_2); ?>
                    шт.)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#panel3" role="tab">на 3+ккв. <br>
                    (<?= count($clients_3); ?> шт.)</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#panel4" role="tab">на комнату<br>
                    (<?= count($clients_30); ?> шт.)</a>
            </li>
        </ul>
        <!-- Tab panels -->
        <div class="tab-content card">
            <!--Panel 1-->
            <div class="tab-pane fade in show active" id="panel1" role="tabpanel">
                <br>
                <?php if ($clients_1) {
                    foreach ($clients_1 as $client_1) { ?>

                        <p><?php echo SaleFilterWidgets::widget([
                                'salefilter' => $client_1,
                                'type' => 'client']); ?></p>
                    <?php }
                } else echo $no_Clients;?>

            </div>
            <!--/.Panel 1-->
            <!--Panel 2-->
            <div class="tab-pane fade" id="panel2" role="tabpanel">
                <br>
                <?php if ($clients_2) {
                    foreach ($clients_2 as $client_2) { ?>

                        <p><?php echo SaleFilterWidgets::widget([
                                'salefilter' => $client_2,
                                'type' => 'client']); ?></p>
                    <?php }
                } else echo $no_Clients; ?>
            </div>
            <!--/.Panel 2-->
            <!--Panel 3-->
            <div class="tab-pane fade" id="panel3" role="tabpanel">
                <br>
                <?php if ($clients_3) {
                    foreach ($clients_3 as $client_3) { ?>

                        <p><?php echo SaleFilterWidgets::widget([
                                'salefilter' => $client_3,
                                'type' => 'client']); ?></p>
                    <?php }
                } else echo $no_Clients; ?>
            </div>
            <div class="tab-pane fade" id="panel4" role="tabpanel">
                <br>
                <?php if ($clients_30) {

                    foreach ($clients_30 as $client_30) { ?>

                        <p><?php echo SaleFilterWidgets::widget([
                                'salefilter' => $client_30,
                                'type' => 'client']); ?></p>
                    <?php }
                } else echo $no_Clients;?>
            </div>
            <!--/.Panel 3-->
        </div>


    </div>
    <div class="col-md-3">
        <div class="row">
            <div class="card text-center" style="width: 22rem;">
                <div class="card-header success-color white-text">
                    <h3><i class="fa fa-check" aria-hidden="true"></i> Совет!</h3>
                </div>
                <div class="card-body">
                    <h4 class="card-title">5 реальных советов по быстрой продаже квартиры...</h4>
                    <a class="btn btn-success btn-sm">Получить</a>
                </div>

            </div>
        </div>

        <div class="row">
            <div class="card text-center" style="width: 22rem;">
                <div class="card-header success-color white-text">
                    <h3><i class="fa fa-check" aria-hidden="true"></i> Совет!</h3>
                </div>
                <div class="card-body">
                    <h4 class="card-title">Оценка реальной стоимости квартиры по телефону с точностью +-2% за 5 минут.</h4>
                    <a class="btn btn-success btn-sm">Получить</a>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="card text-center" style="width: 22rem;">
                <div class="card-header info-color white-text">
                    <h3><i class="fa fa-check" aria-hidden="true"></i> Бесплатная консультация</h3>
                </div>
                <div class="card-body">
                    <h4 class="card-title">Расчет стоимости доплаты в случае обмена 1к на 2к, 2к на 3к и т.д.</h4>
                    <a class="btn btn-info btn-sm">Получить</a>
                </div>

            </div>
        </div>


    </div>
</div>
