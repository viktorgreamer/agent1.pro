<?php
use app\models\SaleFilters;
use app\models\Renders;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $mini_salefilter SaleFilters */

?>

<div id="removesticky" class="sticky z-depth-5"
     style="margin-bottom: 20px; background-color: whitesmoke;">
    <div class="container">
        <h3 class="font-bold light-blue-text my-3">Смотрите также:</h3>
        <p align="justify"><?php
            if ($salefilter->relevanted_ids) {
            $salefilters = $salefilter->getRelevantedIds_Web();
            foreach ($salefilters  as $mini_salefilter) {
            echo $this->render('_price_list_item', compact('mini_salefilter')); ?>

        <hr>

        <? }
        } else echo " nothing to show";
        ?>

        </p>

    </div>

</div>
<div id="YourStopperId" style="position: absolute; bottom: 0px; height: 30px;"></div>


<?php

$script = <<< JS
 $(function () {
        $(".sticky").sticky({
            topSpacing: 55
            , zIndex: 2
            , stopper: "#YourStopperId"
        });
    });
if ($(window).width() < 990 ) $('#removesticky').removeClass('sticky');
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);



