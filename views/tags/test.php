<?php
$parent_id = 4;
$realtags = [];
$type = 'sale';

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Tags;
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
    <ul class="nav nav-tabs nav-justified">
        <? foreach ($tags_groups as $key => $tags_group) { ?>
            <li class="nav-item">
                <a class="nav-link <? $n++;
                if ($n == 1) echo "active"; ?>" data-toggle="tab" href="#<?= $unique_pre; ?><?= $key + 1; ?>"
                   role="tab"> <?= $tags_group['name'] ?></a>
            </li>
        <? } ?>
    </ul>


    <div class="tab-content card">
        <? $n = 0;
        foreach ($tags_groups as $key => $tags_group) { ?>

            <div class="tab-pane fade <? $n++;
            if ($n == 1) echo "show active"; ?>" id="<?= $unique_pre; ?><?= $key + 1; ?>" role="tabpanel">
                <br>
                <?php
                $tags = $tags_group['tags'];
                if (count($tags) != 0) {
                    $a_grouped_tags = array_group_by($tags, 'a_type');
                  //  my_var_dump($tags);
                    foreach ($a_grouped_tags as $tags) { ?>
                        <hr style="margin-top: 0px; margin-bottom: 0px;">
                        <?

                        if ($tags[0]['a_type']) {
                            echo "<h2>".Tags::A_TYPES[$tags[0]['a_type']]."</h2>";
                            foreach ($tags as $tag) {
                                echo "  " . \app\models\Tags::renderActiveTag($parent_id, $tag, $realtags, $type, $tags[0]['a_type']);
                            }
                        } else {
                            echo info('ДРУГИЕ', 'primary');
                            foreach ($tags as $tag) {
                                echo "  " . \app\models\Tags::renderActiveTag($parent_id, $tag, $realtags, $type);
                            }
                        }

                    }
                }

                ?>
            </div>
        <? } ?>

    </div>

    <hr>
    <a href="<?= Url::toRoute(['/tags/create']) ?>" target="_blank"> +new tag</a>

    <!--    <button type="button" class="btn btn-success btn-sm" data-dismiss="modal" aria-label="Close"-->
    <!--            id="closeModal">-->
    <!--        Закрыть-->
    <!--    </button>-->

</div><!-- tags-quick-form -->

