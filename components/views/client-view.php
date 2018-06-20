<?php
 // $salefilter = \app\models\SaleFilters::findOne($salefilter->id);
// my_var_dump($salefilter->rooms_count);
$body = " Куплю " . \app\models\RenderSalefilters::RoomsCount($salefilter->rooms_count);

if ($salefilter->price_up) $body .= ", цена до <b>" . \app\models\Renders::Price($salefilter->price_up) . " </b>";
if ($salefilter->grossarea_down) $body .= ", площадью от <b>" . $salefilter->grossarea_down . " </b> м2";
if ($salefilter->year_down) $body .= ",дом нестарше <b>" . $salefilter->year_down . " </b> г.п.";


if ($salefilter->komment) $body .= "<br>" . $salefilter->komment;

?>

<div class="card blue lighten-3 z-depth-2">
    <div class="card-body">
        <h3><?= $salefilter->name; ?></h3>
        <h5><?= $salefilter->komment; ?></h5>
        <p class="white-text mb-0"><?php
            echo $body;
            ?></p>
        <?php echo \app\components\TagsWidgets::widget(['tags' => \app\models\Methods::convertToArrayWithBorders($salefilter->plus_tags)]); ?>

    </div>
</div>
