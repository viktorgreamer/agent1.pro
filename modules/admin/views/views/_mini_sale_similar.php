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
            <div class="col-sm-1"><?= $sale->renderSource(); ?> </div>
            <div class="col-sm-3">
                <?php echo \app\models\Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?>
                <?php echo $sale->renderAddress(); ?>
                <small>
                    <?php echo $sale->renderFloors();
                    echo $sale->renderHouse_type(); ?>
                    <?php echo $sale->renderAreas(); ?>
                    <br> <?php echo Renders::Days_ago($sale->date_start); ?>
                    <?php if ($sale->disactive == 1) echo "<span class='badge badge-pill red'>удалено</span> " . Renders::Days_ago($sale->date_of_die, 'danger'); ?>
                </small>
            </div>
            <div class="col-sm-3">
                <div style='text-align: center;'><b> <?php echo Renders::Price($sale->price); ?> </b></div>
                <?php if ($contacts) echo $sale->renderContacts('mini'); ?>

                <?php if (($stat) and ($sale->grossarea)) info(round(0.001 * $sale->price / $sale->grossarea, -1) . " рубл./m2", 'alert'); ?>
            </div>
        <div class="col-sm-3">
               <?php if ($controls) {
                echo $this->render('_sale_controls1', ['sale' => $sale, 'salefilter' => $salefilter]);
                ?>
                   <textarea id="id<?= $sale->id; ?>" hidden><?php echo strip_tags($sale->title_to_copy); ?> </textarea>


                   <?php
            }
            ?>
        </div>

        </div>
        <hr style="margin-top: 0px;margin-bottom: 0px;">
    <? }
} ?>
<?php



