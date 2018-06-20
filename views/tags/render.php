<?php
use app\models\Tags;
use app\models\RealTags;
use app\models\Addresses;
use app\models\SaleFilters;

// если пришел sale_id о рендерим tags for sale
if ($sale_id) {
    $Realtags = explode(',', RealTags::find()
        ->where(['sale_id' => $sale_id])
        ->one()->tags_id);
    $this->tags = '';
    foreach ($Realtags as $realtag) {
        $tag = Tags::findOne($realtag->tag_id);
        $this->tags .= "<span class=\"label label-" . $tag->color . "\">#" . $tag->name . "</span>";
    }
}

?>
    <br>
<?php
// если пришел $id_address
if ($id_address) {
    foreach (explode(',', Addresses::findOne($id_address)->tags_id) as $realtag) {
        $tag = Tags::findOne($realtag);
        $tags .= "<span class=\"label label-" . $tag->color . "\">#" . $tag->name . "</span>";
    }
}
?>
    <br>
<?php
// если пришел salefilter_id
if ($salefilter_id) {
    foreach (explode(',', SaleFilters::findOne($salefilter_id)->tags_id) as $realtag) {
        $tag = Tags::findOne($realtag);
        $tags .= "<span class=\"label label-" . $tag->color . "\">#" . $tag->name . "</span>";
    }
}

