<?php
/* @var $model app\models\Sale */

/* @var $sale app\models\Sale */


use app\models\Renders;

?>

    <div class="row" id="row_mini_sale<?= $sale->id; ?>" <?php if ($sale->disactive == 1) echo " style='background-color: gainsboro'"; ?>>

        <div class="col-sm-6 col-md-6 col-6 col-lg-6">
            <div class="row">
                <div class="col-sm-2" style="padding-left: 15px; padding-right: 5px;">
                    <?= $sale->renderSource(); ?>
                    <?php if ($sale->similar->moderated == \app\models\SaleSimilar::MODERATED) echo Renders::MODERATED(); ?>

                </div>

                <div class="col-sm-8" style="padding-left: 5px; padding-right: 5px;">
                    <?php echo \app\models\Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count]; ?>
                    <?php echo $sale->renderAddress(); ?>
                    <?php echo $sale->renderFloors();
                    echo $sale->renderHouse_type(); ?>
                    <?php echo $sale->renderAreas(); ?>
                    <br> <?php echo Renders::Days_ago($sale->date_start); ?>
                    <?php if ($sale->disactive == 1) echo "<span class='badge badge-pill red'>удалено</span> " . Renders::Days_ago($sale->date_of_die, 'danger'); ?>
                </div>
            </div>
        </div>

        <div class="col-sm-4 col-md-4 col-4 col-lg-4">
            <div style='text-align: center;'><b> <?php echo Renders::Price($sale->price); ?> </b></div>
            <?php if ($contacts) echo $sale->renderContacts('mini'); ?>


        </div>
        <div class="col-sm-2 col-md-2 col-2 col-lg-2">
            <?php if ($controls) {
                echo $this->render('_sale_controls', ['sale' => $sale, 'salefilter' => $salefilter]);
                ?>
                <?php if ($sale->similar->status == \app\models\SaleSimilar::SOLD) echo Renders::SOLD(); ?>
                <p id="id<?= $sale->id; ?>" hidden><?php echo strip_tags($sale->title_to_copy); ?> </p>


                <?php
            }
            ?>
        </div>

    </div>


    <hr style="margin-top: 0px;margin-bottom: 0px;">

<?php

