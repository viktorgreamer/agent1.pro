<?php
/* @var $model app\models\Sale */
/* @var $sale app\models\Sale */
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\models\Renders;
use app\models\Sale;


?>

        <?php $address = \app\models\Addresses::findOne($sale->id_address); ?>
        <div class="row" id="row_<?= $sale->id; ?>">
            <div class="col-sm-2"><?= $sale->renderUrl(); ?> </div>
            <div class="col-sm-7">
                <?php echo Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?>
                <?php echo $sale->renderAddress(); ?>
                <small>
                    <br> <?php echo $sale->renderFloors();
                    echo $sale->renderHouse_type(); ?>
                    <?php echo $sale->renderAreas(); ?>
                    <br> <?php echo $sale->days_ago; ?>
                </small>
            </div>
            <div class="col-sm-3">  <?php echo Renders::Price($sale->price); ?>
                <?php if ($contacts) echo $sale->renderContacts('mini'); ?>

                <div class="col-sm-1">
                    <?php if ($controls) echo $this->render('_sale_controls', compact(['sale', 'salefilter', 'salelist'])); ?>

                </div>
            </div>

<?php





