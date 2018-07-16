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

?>
    <div class="modal fade" id="TagsModal_moder_<?php echo $model->id_address; ?>" tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fluid" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <?php
                    if ($similarsales = $model->similarsales) {
                        $tags = $model->similar->tags;
                        //   if (count($ids) == 1) $description = true;
                        $description = true;
                        $realtags = [];
                        foreach ($similarsales as $sale) {
                            echo "<b>" . $sale->address . "</b><br>";
                         //   $realtags = array_merge($realtags, $sale->AutoLoadTags(true));

                            if ($description) echo "<br>" . $sale->description;
                            // $sale_id = $sale->id;
                            echo "<hr>";
                        }

                        foreach (Tags::find()->where(['in', 'id', array_unique($realtags)])->all() as $tag) {
                            echo Tags::renderActiveTag($model->id, $tag, $tags, 'sale', $tag->a_type) . " ";
                        }
                        echo $this->render('//tags/quick-add-form-alternative', [
                            'parent_id' => $model->id,
                            'realtags' => $model->tagsSale,
                            'type' => 'sale',
                            'id_address' => $boolAddress

                        ]);
                    } else {
                        my_var_dump($similarsales);
                    }


                    ?>
                </div>
            </div>
        </div>
    </div>
<?

echo $this->render('_sale-table', ['model' => $model, 'controls' => true]);