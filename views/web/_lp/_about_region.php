<?php
use yii\helpers\Url;
use app\models\SaleFilters;
?>
<?php
$region = SaleFilters::findOne($salefilter->regions);
?>

<div class="view intro hm-black-light jarallax" data-jarallax='{"speed": 0.2}'
     style="background-image: url(<?= Url::to("@web/images/lp/" . $region->photo, true); ?>); height: 700px;">
    <div class="full-bg-img">
        <div class="container">
            <div style="height:500px">
                <div class="row mt-5">
                    <div class="col-md-7 wow fadeIn mb-3">

                        <div class="intro-info-content text-center" style="background-color: whitesmoke;">
                            <h1 class="font-bold pink-text mb-3"><?php echo $region->name; ?></h1>
                            <div class="container">
                                <p align="justify" style="text-indent: 50px;">
                                <?php echo $region->komment; ?></div>

                        </div>
                    </div>
                    <div class="col-md-5" style="background-color: whitesmoke;">

                        <?php // показать фильтры из данного района
                        echo $this->render('_list_locality_filters', compact('salefilter')); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

