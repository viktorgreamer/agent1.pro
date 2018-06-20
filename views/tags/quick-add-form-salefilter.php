<?php


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;


/* @var $this yii\web\View */
/* @var $model app\models\Tags */
/* @var $form ActiveForm */
/*$session = Yii::$app->session;
//echo $session->get('city_module');
$types = explode(',', \app\models\Tags::TYPES);
// берем все tags которые могут относится к адресу
$tags = \app\models\Tags::find()
    ->where(['in','locality' , ['default',$session->get('city_module')]])
    ->andwhere(['in','type', ['locality', 'building', 'plan']])
    ->orderBy('type')
    ->all();*/
// берем tags которые относятся к address
$tags_groups = \app\models\Tags::getGroupedTags('all');


// TODO: добавить потом локальные Local_tags : например " район панковка" или 361 рем завод, или зайстрокщик скандинавия!
// берем уже отмеченные tags для отображения на экране
$Realtags = explode(',', \app\models\SaleFilters::find()->where(['id' => $id])->one()->tags_id);

?>
<div class="tags-quick-add-form">

    <?php
    foreach ($tags_groups as $tags_group) {
        ?>
        <div class="row">
            <div class="card">
                <div class="card-header success-color white-text">
                    <?= $tags_group['name'] ?>
                </div>
                <div class="card-body">
                    <?php

                    $tags = $tags_group['tags'];

                    if (count($tags) != 0) {
                        foreach ($tags as $tag) {
                            ?>
                            <? if (in_array($tag->id, $Realtags)) { ?> <i class="fa fa-check success-color"
                                                                          aria-hidden="true"></i> <?php } ?>
                            <a href="#" class="tags-action-button-salefilter"
                               data-salefilter_id="<?= $id ?>"
                               data-tag_id="<?= $tag->id ?>">  <span
                                        class="badge badge-<?= $tag->color; ?> <? if (!in_array($tag->id, $Realtags)) echo "half-opacity"; ?>">
                              <?= $tag->name; ?></span></a>


                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
        <?php
    }


    ?>
    <hr>
    <a href="<?= Url::toRoute(['/tags/create']) ?>" target="_blank"> +new tag</a>
    <button type="button" class="btn btn-success btn-sm" data-dismiss="modal" aria-label="Close"
            id="closeModal">
        Закрыть
    </button>

</div><!-- tags-quick-form -->
