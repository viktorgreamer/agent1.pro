<?php

use app\components\Mdb;
use app\models\Tags;
use app\models\Sale;

/* @var $sale Sale */
/* @var $model Sale */

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
            <div class="row">
                <?php
                if ($similarsales = $model->similarsales) {
                $tags = $model->similar->tags;
                //   if (count($ids) == 1) $description = true;
                $description = true;
                $realtags = [];
                foreach ($similarsales

                as $sale) { ?>
                <div class="col-lg-4 col-xs-12 col-md-6 col-12 col-sm-12">
                    <?
                    echo "<b>" . $sale->address . "</b><br>";
                    $realtags = array_merge($realtags, $sale->AutoLoadTags(true));

                    // if ($description) echo "<br>" . $sale->description;
                    // $sale_id = $sale->id;
                    echo "</div>";
                    }
                    $realtags = array_merge($realtags, $model->AutoLoadTags(true));

                    } else {
                    if (!($tags = $model->similar->tags)) {
                        $tags = [];
                    }; ?>
                    <div class="col-lg-4 col-xs-12 col-md-6 col-12 col-sm-12">

                        <?php

                        $realtags = $model->AutoLoadTags(true);
                        echo "</div>";
                        } ?>
                    </div>
                    <?


                    foreach (Tags::find()->where(['in', 'id', array_unique($realtags)])->all() as $tag) {
                        echo Tags::renderActiveTag($model->id, $tag, $tags, 'sale', $tag->a_type) . " ";
                    }

                    echo $this->render('//tags/quick-add-form-alternative', [
                        'parent_id' => $model->id,
                        'realtags' => $model->tagsSale,
                        'type' => 'sale',
                        'id_address' => $boolAddress

                    ]);


                    echo \yii\helpers\Html::button(Mdb::Fa('close'), ['class' => CLASS_BUTTON_DANGER, 'data-dismiss' => 'modal']);
                    ?>

                </div>
            </div>
        </div>
    </div>
<?

echo $this->render('_sale-table', ['model' => $model, 'controls' => true]);