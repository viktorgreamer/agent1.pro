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
        <?php $address = \app\models\Addresses::findOne($sale->id_address); ?>
        <div class="row" id="row_<?= $sale->id; ?>">
            <div class="col-sm-2"><?= $sale->renderUrl(); ?> </div>
            <div class="col-sm-7">
                <?php echo \app\models\Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?>
                <?php if ($address) echo $address->address; else echo $sale->address; ?>
                <small>
                    <br> <?php echo $sale->renderFloors();
                    echo $sale->renderHouse_type(); ?>
                    <?php echo $sale->renderAreas(); ?>
                    <br> <?php echo $sale->days_ago; ?>
                </small>
            </div>
            <div class="col-sm-3">  <?php info(Renders::Price($sale->price), 'alert'); ?>
                <?php if ($contacts) echo $sale->renderContacts('mini'); ?>

                <?php if (($stat) and ($sale->grossarea)) info(round(0.001 * $sale->price / $sale->grossarea, -1) . " рубл./m2", 'alert'); ?>
            </div>

        </div>
        <hr>
    <? }
} ?>
<?php





