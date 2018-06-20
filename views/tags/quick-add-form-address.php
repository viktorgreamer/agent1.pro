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

if (empty(Yii::$app->cache->get('tags_address'))) {
    Yii::$app->cache->set('tags_address', \app\models\Tags::getGroupedTags('address'));
}
$tags_groups = Yii::$app->cache->get('tags_address');

// TODO: добавить потом локальные Local_tags : например " район панковка" или 361 рем завод, или зайстрокщик скандинавия!
// берем уже отмеченные tags для отображения на экране
// $realtags =  \app\models\Addresses::findOne($id)->tags;

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
                            foreach ($tags as $tag) { ?>
                                <? if ((!empty($realtags)) and (in_array($tag->id, $realtags))) { ?> <i
                                        class="fa fa-check" id="tag_in_address_<?= $tag->id ?>"
                                        aria-hidden="true"></i> <?php } ?>
                                <a href="#" class="tags-action-button-address" data-address_id="<?= $id ?>"
                                   data-tag_id="<?= $tag->id ?>">  <span
                                            class="badge badge-<?= $tag->color; ?> <? if ((!empty($realtags)) and (in_array($tag->id, $realtags))) echo 'border border-primary'; ?>"
                                            id="tag_address_<?= $tag->id ?>"
                                            style='margin-top: 7px; margin-bottom: 7px;'><?= $tag->name; ?></span></a>

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

<?php
$tags_class_appearance = Tags::ACTIVE_TAG_CLASS_APPEARENCE;
$tags_class_exit = Tags::ACTIVE_TAG_CLASS_EXIT;
$script = <<< JS
tags_class_appearance = $tags_class_appearance
tags_class_exit = $tags_class_exit



JS;
$this->registerJs($script, yii\web\View::POS_READY);
