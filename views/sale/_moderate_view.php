<?php

use app\components\Mdb;
use app\models\Tags;
use app\models\Sale;


/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.02.2018
 * Time: 9:10
 */

// echo "<br>" . $model->similarNew->similar_ids;/
//my_var_dump(\app\models\Tags::convertToArray($model->similarNew->similar_ids));
if ($model->addresses) $boolAddress = true;
?>
    <a id="#AddTagsAddress_mod_<?php echo $model->id_address; ?>" type="button" href="#"
       data-toggle="modal"
       data-target="#TagsModal_moder_<?= $model->id_address ?>"> <i
                class="fa fa-edit green-text fa-2x" aria-hidden="true">ОКНО МОДЕРАЦИИ</i>
    </a>
<?php
if ($model->similarNew) {
//    $description = '';
//    $sales = \app\models\Sale::find()->where(['in', 'id', \app\models\Tags::convertToArray($model->similarNew->similar_ids)])->all();
//    foreach ($sales as $sale) {
//        $description .= "<br><small>" . $sale->description . "</small>".Mdb::ProgresBar('bg-primary');
//    }
}
?>
    <div class="modal fade" id="TagsModal_moder_<?php echo $model->id_address; ?>" tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fluid" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php
                    //  my_var_dump($model->similarNew->similar_ids_all);
                    if ($model->similarNew->similar_ids_all) {
                        $sales = Sale::find()->where(['in', 'id', Tags::convertToArray($model->similarNew->similar_ids_all)])->groupBy('description')->all();
                    } else {
                        //  my_var_dump($model->similarNew->similar_ids);
                        $ids = Tags::convertToArray($model->similarNew->similar_ids);
                        if (count($ids) == 1) $description = true;
                        $sales = Sale::find()->where(['in', 'id', Tags::convertToArray($model->similarNew->similar_ids)])->all();
                    }

                    $realtags = [];
                    foreach ($sales as $sale) {
                        echo "<b>" . $sale->address . "</b><br>";
                        $realtags = array_merge($realtags, $sale->AutoLoadTags(true));

                        if ($description) echo "<br>" . $sale->description;
                        // $sale_id = $sale->id;
                        echo "<hr>";
                    }

                    foreach (Tags::find()->where(['in', 'id', array_unique($realtags)])->all() as $tag) {
                        echo Tags::renderActiveTag($model->id, $tag, $model->similarNew->tags, 'sale', $tag->a_type) . " ";
                    }
                    ?>
                    <!--                    --><? // echo $description ; ?>
                    <? echo $this->render('//tags/quick-add-form', [
                        'parent_id' => $model->id,
                        'realtags' => $model->tagsSale,
                        'type' => 'sale',
                        'id_address' => $boolAddress

                    ]); ?>


                </div>
            </div>
        </div>
    </div>
<?

echo $this->render('_sale-table-moderate', ['model' => $model, 'controls' => true]);