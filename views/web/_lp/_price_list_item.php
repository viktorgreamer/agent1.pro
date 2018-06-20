<?php
use app\models\SaleFilters;
use app\models\Renders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $mini_salefilter SaleFilters */
?>
<div class="row">
    <div class="col-12">
        <a href="<?= Url::to(['web/search-by', 'id' => $mini_salefilter->id]); ?>">
            <h5>
                <span class="badge cyan animated pulse "><?php echo $mini_salefilter->name; ?> </span>
            </h5>
        </a>
        <h6><?php echo $mini_salefilter->komment; ?></h6>
    </div>
</div>
<div class="row">
    <div class="col-9">
        от <span
            class="badge badge-pill light-blue"><?php echo Renders::Price($mini_salefilter->getMinPrice()); ?></span>
        до <span
            class="badge badge-pill light-blue"><?php echo Renders::Price($mini_salefilter->getMaxPrice()); ?></span>
    </div>
    <div class="col-3"><span
            class="badge badge-pill pink"><?php echo $mini_salefilter->getCount(); ?>
            ШТ.</span>
    </div>

</div>
