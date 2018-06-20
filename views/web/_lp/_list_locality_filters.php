<?php

/* @var $this yii\web\View */
/* @var $mini_salefilter SaleFilters */

?>
<h3 class="font-bold light-blue-text my-3">Также в этом микрорайоне</h3>
<?php
$salefilters = $salefilter->getLocalityFilters();
if ($salefilters) {
    foreach ($salefilters as $mini_salefilter) {
        echo $this->render('_price_list_item', compact('mini_salefilter'));
       echo "<hr>";
    }
}
?>

