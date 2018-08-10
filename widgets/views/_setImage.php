<?php
use app\models\Actions;

$images = \yii\helpers\FileHelper::findFiles(Yii::getAlias('@app') . "/web/images/salefilters/thumbs/");
 // my_var_dump($images);
//
//
?>

    <!-- Button trigger modal -->
    <button type="button" class="<?= CLASS_BUTTON;?> " data-toggle="modal" data-target="#basicExampleModal<?= $id;?>">
      ADD PHOTO
    </button>

    <!-- Modal -->
    <div class="modal fade" id="basicExampleModal<?= $id;?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel<?= $id;?>" aria-hidden="true">
        <div class="modal-dialog modal-fluid"  role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel<?= $id;?>">Добавить фото</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">


<?
if ($images) { ?>
    <div class="row">
        <?
        foreach ($images as $image) { ?>

            <div class="col-lg-3">
                <?= \app\models\Actions::renderChangeStatus($id, Actions::SALEFILTER,
                    \app\models\Actions::SALEFILTER_IMAGE, basename($image),
                    \yii\helpers\Html::img("/web/images/salefilters/thumbs/".basename($image), ['width' => 200]));?>

            </div>
        <? } ?>
    </div>
    <?php
}
?>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>

                </div>
            </div>
        </div>
    </div>
