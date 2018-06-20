<?php


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Tags;

/* @var $this \yii\web\View */

//$type = 'sale';
//$parent_id = $id; // add tags to sale
if (!$id_address) {
    $tags_groups = \app\models\Tags::getGroupedTags('');
    // echo span("NO ID ADDRESS");
} else $tags_groups = \app\models\Tags::getGroupedTags($type);

$unique_pre = substr(md5(rand()), 0, 10);
// TODO: добавить потом локальные Local_tags : например " район панковка" или 361 рем завод, или зайстрокщик скандинавия!
if (!$realtags) $realtags = [];
?>


<div class="tags-quick-add-form">


    <ul class="nav  nav-tabs nav-justified">
        <? foreach ($tags_groups as $key => $tags_group) { ?>

            <li class="nav-item">
                <a class="nav-link tag-panel<? $n++;
                if ($n == 1) echo "active"; ?>" data-toggle="tab" href="#<?= $unique_pre; ?><?= $key + 1; ?>"
                   role="tab"><?= Html::img("/web/icons/" . $tags_group['icon'], ['width' => 32]); ?>
                    <div class="d-none d-lg-block"> <?= $tags_group['name'] ?></div>
                </a>
            </li>

        <? } ?>
    </ul>

    <div class="tab-content card">
        <? $n = 0;
        foreach ($tags_groups as $key => $tags_group) { ?>

            <div class="tab-pane fade <? $n++;
            if ($n == 1) echo "show active"; ?>" id="<?= $unique_pre; ?><?= $key + 1; ?>" role="tabpanel">
                <br>
                <div class="row ">
                    <?php
                    $tags = $tags_group['tags'];
                    if (count($tags) != 0) {
                        $a_grouped_tags = array_group_by($tags, 'a_type');
                        //  my_var_dump($tags);
                        foreach ($a_grouped_tags as $tags) { ?>
                            <div class="col-lg-6 col-md-12 col-sm-12 col-12 table-bordered">
                                <? if ($tags[0]['a_type']) {
                                    echo "<h3>" . Tags::A_TYPES[$tags[0]['a_type']] . "</h3>";
                                    foreach ($tags as $tag) {
                                        echo "  " . Tags::renderActiveTagNewer($parent_id, $tag, $realtags, $type, $tags[0]['a_type']);
                                    }
                                } else {
                                    echo "<h3 class='green-text'>ДРУГИЕ</h3>";
                                  //  echo info('ДРУГИЕ', 'primary');
                                    foreach ($tags as $tag) {
                                        echo "  " . Tags::renderActiveTagNewer($parent_id, $tag, $realtags, $type);
                                    }
                                }
                                ?>
                            </div>
                            <?

                        }
                    } ?>

                </div>
            </div>
        <? } ?>

    </div>

    <a href="<?= Url::toRoute(['/tags/create']) ?>" target="_blank"> +new tag</a>

    <!--    <button type="button" class="btn btn-success btn-sm" data-dismiss="modal" aria-label="Close"-->
    <!--            id="closeModal">-->
    <!--        Закрыть-->
    <!--    </button>-->

</div>

