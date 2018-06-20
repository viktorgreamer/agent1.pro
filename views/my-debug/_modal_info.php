<?php

/* @var $this \yii\web\View */
use app\components\Mdb;
$id = substr(md5(rand()), 0, 10);
//$this->registerMetaTag(['name' => 'Content-type', 'content' => 'application/json']);
?>

<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#<?= $id ?>">
   <?= $icon; ?>
</button>

<!-- Modal -->
<div class="modal fade" id="<?= $id ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <?= $message ; ?>
            </div>

        </div>
    </div>
</div>
