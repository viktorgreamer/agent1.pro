<?php
use app\models\SaleFilters;
use app\models\Renders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $salefilter SaleFilters */
?>
<a href="<?= Url::to(['web/search-by', 'id' => $salefilter->id]); ?>"></a>


<div class="card card-cascade narrower">

    <!-- Card image -->
    <div class="view view-cascade overlay">
        <img class="card-img-top" src="<?= Url::to("web/images/salefilters/thumbs/".$salefilter->image); ?>" alt="<?php echo $salefilter->name; ?>">
        <a>
            <div class="mask rgba-white-slight"></div>
        </a>
    </div>

    <!-- Card content -->
    <div class="card-body card-body-cascade">

        <!-- Label -->
         <h4 class="card-title"><?php echo $salefilter->name; ?></h4>
        <div class="row">
            <div class="col-9">
                от <?php echo Renders::Price($salefilter->getMinPrice()); ?>
                до <?php echo Renders::Price($salefilter->getMaxPrice()); ?>
            </div>
            <div class="col-3"><span
                        class="badge badge-pill primary-color"><?php echo $salefilter->getCount(); ?>
                    ШТ.</span>
            </div>
            <div class="col-12">
                <div class="text-primary float-right "><small>Обновлен <?php echo Yii::$app->formatter->asRelativeTime($salefilter->time); ?></small></div>

            </div>

        </div>


    </div>

</div>

