<?php
use app\models\SaleFilters;
use app\models\Renders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salefilter SaleFilters */
?>
<a href="<?= Url::to(['web/search-by', 'id' => $salefilter->id]); ?>">
            <h4>
             <?php echo $salefilter->name; ?>
            </h4>
        </a>
        <h6><?php echo $salefilter->komment; ?></h6>

<div class="row">
    <div class="col-9">
        от <?php echo Renders::Price($salefilter->getMinPrice()); ?>
        до <?php echo Renders::Price($salefilter->getMaxPrice()); ?>
    </div>
    <div class="col-3"><span
            class="badge badge-pill pink"><?php echo $salefilter->getCount(); ?>
            ШТ.</span>
    </div>

</div>
