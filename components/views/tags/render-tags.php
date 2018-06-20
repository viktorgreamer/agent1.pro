<?php
use app\models\Tags;
use app\models\RealTags;
use app\models\Addresses;
use app\models\SaleFilters;
use app\models\SaleLists;
use yii\helpers\ArrayHelper;
$tags_render = '';
// если пришел sale_id о рендерим tags for sale
if ($tags) {

    $pre = '#';
    if (empty(Yii::$app->cache->get('tags'))) {
        Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());

    }
    $all_tags = Yii::$app->cache->get('tags');


    $new_tags = [];
   // echo implode(",", $tags);
    foreach ($tags as $tag) {
        array_push($new_tags, $all_tags[$tag]);
    }
  //  my_var_dump($all_tags[20]);
  //  my_var_dump(\yii\helpers\ArrayHelper::getColumn($new_tags, 'id'));


    $grouped_tags = array_group_by($new_tags, 'type');
  //  echo " <br>";
    foreach ($grouped_tags as $key => $tags) {

        $tags_render .= Tags::TYPES_ARRAY[$key] . ": ";
        foreach ($tags as $tag) {
           // echo "," . $tag['id'];

            $tags_render .= "<span class=\"badge badge-" . $tag['color'] . "\">" . $pre . "" . $tag['name'] . "</span> ";
            //  $tags_render .=  $all_tags[$tag]['name'];
            if ($moderate) $tags_render .=" ".$tag['id']." ";

        }
       if ($br !== false) $tags_render .= "<br>";
    }
    //   echo "<br>".implode(",", \yii\helpers\ArrayHelper::getColumn($tags, 'id'));

}

?>

<?php
// если пришел $id_address
if ($id_address) {
    $address = Addresses::findOne($id_address);
    $Realtags = explode(',', $address->tags_id);
    $tags = '';
    foreach ($Realtags as $realtag) {
        $tag = Tags::findOne($realtag);
        $tags .= " <span class=\"badge badge-" . $tag->color . "\">#" . $tag->name . "</span>";
    }
    echo $tags;
}

?>

<?php
// если пришел salefilter_id
if ($salefilter_tags_id) {
    echo Tags::render($salefilter_tags_id);
}
if ($salelist_tags_id) {
    echo Tags::render($salelist_tags_id);


}

echo $tags_render;

