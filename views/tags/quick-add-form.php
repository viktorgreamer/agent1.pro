<?php


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

            <li class="nav-item ">
                <a class="nav-link tag-panel<? $n++;
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
                                echo "  " . Tags::renderActiveTag($parent_id, $tag, $realtags, $type, $tags[0]['a_type']);
                            }
                        } else {
                            echo info('ДРУГИЕ', 'primary');
                            foreach ($tags as $tag) {
                                echo "  " . Tags::renderActiveTag($parent_id, $tag, $realtags, $type);
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


// добавление или удаление текущего tags для sale
$('.tags-action-button').on('click', function (e) {
e.preventDefault();
var parent_id = $(this).data('parent_id');
var tag_id = $(this).data('tag_id');
var type = $(this).data('type');
var a_type = $(this).data('a_type');
var selector = '.tag_' + type + '_' + parent_id + '_' + tag_id;
console.log('type=' + type + ', parent_id =' + parent_id + ', tag_id=' + tag_id + ', a_type =' + a_type + ', selector = ' + selector);

$.ajax({
url: '/real-tags/tag',
data: {parent_id: parent_id, tag_id: tag_id, type: type},
type: 'get',
success: function (res) {
// toastr.success(res);
if (a_type) {
console.log('РАБОТАЕМ С A_TYpe');

if ($("." + a_type).hasClass('animated pulse infinite z-depth-5')) {
if ((type != 'plus_search') && (a_type != 'minus_search')) {
if (!$(selector).hasClass('animated pulse infinite z-depth-5')) addClass = true; else addClass = false;
$("." + a_type).removeClass('animated pulse infinite z-depth-5');
if (addClass) $(selector).addClass('animated pulse infinite z-depth-5');
} else {
// console.log('РАБОТАЕМ С type=' + type);
$(selector).toggleClass('animated pulse infinite z-depth-5');
}
}
else {
$(selector).toggleClass('animated pulse infinite z-depth-5');
}


} else $(selector).toggleClass('animated pulse infinite z-depth-5');

if (type == 'plus_search') {
$("#plus_searching_tags").val(res);
$('.searching_tags_div').load(encodeURI('/tags/render-tags'));

}
if (type == 'minus_search') {
$("#minus_searching_tags").val(res);
$('.searching_tags_div').load(encodeURI('/tags/render-tags'));
}
}
,

error: function () {
alert('error')
}
});
this.disabled = true;
});
