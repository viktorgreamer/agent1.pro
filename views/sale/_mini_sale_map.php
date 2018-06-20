<?php
/* @var $model app\models\Sale */
/* @var $sale app\models\Sale */
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\models\Renders;

?>

<?php if ($sales) {
    foreach ($sales as $sale) { ?>
        <div class="row"
             id="row_mini_sale<?= $sale->id; ?>" <?php if ($sale->disactive == 1) echo " style='background-color: gainsboro'"; ?>>

            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-2" style="padding-left: 15px; padding-right: 5px;"><?= $sale->renderSource(); ?> </div>
                    <div class="col-sm-8" style="padding-left: 5px; padding-right: 5px;">
                        <?php echo \app\models\Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?>
                        <?php echo $sale->renderAddress(); ?>

                            <?php echo $sale->renderFloors();
                            echo $sale->renderHouse_type(); ?>
                            <?php echo $sale->renderAreas(); ?>
                            <br> <?php echo Renders::Days_ago($sale->date_start); ?>
                            <?php if ($sale->disactive == 1) echo "<span class='badge badge-pill red'>удалено</span> " . Renders::Days_ago($sale->date_of_die, 'danger'); ?>

                    </div>
                </div>
            </div>

            <div class="col-sm-2">
                <div style='text-align: center;'><b> <?php echo Renders::Price($sale->price); ?> </b></div>
                <?php if ($contacts) echo $sale->renderContacts('mini'); ?>

                <?php if (($stat) and ($sale->grossarea)) info(round(0.001 * $sale->price / $sale->grossarea, -1) . " рубл./m2", 'alert'); ?>
            </div>
            <div class="col-sm-3">
                <?php if ($controls) {
                    echo $this->render('_sale_controls', ['sale' => $sale, 'salefilter' => $salefilter]);
                    ?>
                    <p id="id<?= $sale->id; ?>" hidden><?php echo strip_tags($sale->title_to_copy); ?> </p>


                    <?php
                }
                ?>
            </div>

        </div>
        <hr style="margin-top: 0px;margin-bottom: 0px;">
    <? }
} ?>
<?php



