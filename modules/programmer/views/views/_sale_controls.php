<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Actions;
use app\components\Mdb;
use app\models\SaleSimilar;
use app\models\Sale;
use app\models\SaleFilters;
use app\models\Geocodetion;
use app\components\IconsDropdownWidgets;


/* @var $sale app\models\Sale */
/* @var $salefilter app\models\SaleFilters */
/* @var $salelist app\models\SaleLists */
?>
<?php
$moderate_list = [];


if ($sale->id_similar) {
    // ставим что SIMILAR MODERATED
    $moderate_list[] = Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATED,
        Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED));
    // ставим что SIMILAR MODERATION_ONCALL
    $moderate_list[] = Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATION_ONCALL,
        Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATION_ONCALL));
} else {
    // ставим что SALE MODERATED
    $moderate_list[] = Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_MODERATED, SaleSimilar::MODERATED,
        Actions::getIcons(Actions::SALE, Actions::SALE_MODERATED));
    // ставим что SALE MODERATION_ONCALL
    $moderate_list[] = Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_MODERATED, SaleSimilar::MODERATION_ONCALL,
        Actions::getIcons(Actions::SALE, Actions::SALE_MODERATION_ONCALL));
}

$moderate_list[] = "<a id=\"#AddmanualAddress" . $sale->id . "\" type=\"button\"
       data-toggle=\"modal\"
       data-target=\"#manualAddress" . $sale->id . "\"><i class=\"fa fa-location-arrow fa-fw fa-2x\" aria-hidden=\"true\" title=\"Установить адрес\"></i>
    </a>";
// ставим что SIMILAR MODERATED
$moderate_list[] = Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_GEOCODATED, Geocodetion::ERROR,
    Actions::getIcons(Actions::SALE, Actions::SALE_GEOCODATION_ERROR));

$moderate_list[] = Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_STATUS, Sale::MAN_SOLD,
    Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_STATUS_SOLD));

if ($sale->agent->person_type == \app\models\Agents::PERSON_TYPE_HOUSEKEEPER) $moderate_list[] = Actions::renderChangeStatus($sale->agent->id, Actions::AGENT, Actions::AGENT_PERSON_TYPE,
    Actions::AGENT_PERSON_TYPE_AGENT, Actions::getIcons(Actions::AGENT, Actions::AGENT_PERSON_TYPE_AGENT));

else $moderate_list[] = Actions::renderChangeStatus($sale->agent->id, Actions::AGENT, Actions::AGENT_PERSON_TYPE,
    Actions::AGENT_PERSON_TYPE_HOUSEKEEPER, Actions::getIcons(Actions::AGENT, Actions::AGENT_PERSON_TYPE_HOUSEKEEPER));

$moderate_list[] = " <a onclick=\"copyToClipboard('#id<?= $sale->id; ?>')\"><i
                    class=\"fa fa-clipboard fa-fw fa-2x\"
                    aria-hidden=\"true\"></i></a>";

echo IconsDropdownWidgets::widget(['color' => 'success', 'list' => $moderate_list]);


// если создан salefilter
if ($salefilter->id != 0) {
    $configs = [
        0 => [
            'attr' => Actions::SALEFILTER_BLACK_LIST_ID,
            'id' => 'id'
        ],
        1 => [
            'attr' => Actions::SALEFILTER_WHITE_LIST_ID,
            'id' => 'id'
        ],
        2 => [
            'attr' => Actions::SALEFILTER_SIMILAR_BLACK_LIST_ID,
            'id' => 'id_similar'
        ],
        3 => [
            'attr' => Actions::SALEFILTER_SIMILAR_WHITE_LIST_ID,
            'id' => 'id_similar'
        ],

        4 => [
            'attr' => Actions::SALEFILTER_BLACK_ID_ADDRESSES,
            'id' => 'id_address'
        ],
        5 => [
            'attr' => Actions::SALEFILTER_PROCESSED_IDS,
            'id' => 'id_address'
        ],


    ];

    $salefilter_list = [];
    foreach ($configs as $config) {
        $is_active = preg_match("/," . $sale[$config['id']] . ",/", $salefilter[Actions::getNameAttribute(Actions::SALEFILTER, $config['attr'])], $output);
        $salefilter_list[] = Actions::renderToggleLists($salefilter->id, Actions::SALEFILTER, $config['attr'], $sale[$config['id']],
            Actions::getIcons(Actions::SALEFILTER, $config['attr'], $is_active));
    }

    if ($sale->id_similar) $salefilter_list[] = "<a class=\"on-control\"
           data-id=\"<?= $salefilter->id ?>\"
           data-id_item=\"<?= $sale->id_similar ?>\"
           data-price=\"<?= $sale->price ?>\">
            <i class=\"fa fa-exclamation red-text fa-2x\" aria-hidden=\"true\"></i><i
                    class=\"fa fa-eye fa-2x\" aria-hidden=\"true\" title=\"Следить за ценой\"></i>
                            </a>";

    echo IconsDropdownWidgets::widget(['color' => 'primary', 'list' => $salefilter_list]);
} ?>


    <div class="modal fade" id="manualAddress<?php echo $sale->id; ?>" tabindex="-1"
         role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">

                    <?= Html::input('text', "input_text_" . $sale->id, '', ['class' => 'form-control', 'id' => "input_text_" . $sale->id]) ?>
                    <?= Html::a('Поиск', '#', ['class' => 'btn btn-success', 'id' => "searching_button" . $sale->id]); ?>

                    <?php
                    $url = Url::toRoute("addresses/quick-search-pjax");
                    $script = <<< JS
$(document).on('click', '#searching_button$sale->id', function (e) {
text = $('#input_text_$sale->id').val();
$('#searchingstreet$sale->id').load(encodeURI('$url?id=$sale->id&address='+text));
});
JS;
                    $this->registerJs($script, yii\web\View::POS_READY);
                    ?>
                    <div id="searchingstreet<?php echo $sale->id; ?>"></div>
                </div>
            </div>
        </div>
    </div>

<?
$script = <<< JS
 $(document).on('click', '#AddmanualAddress$sale->id', function (e) {
$("#manualAddress$sale->id").modal('show');
});
 $(document).on('click', '.set-moderated', function (e) {
e.preventDefault();
var id = $(this).data('id');


$.ajax({
url: '/sale/set-moderated',
data: {id: id},
type: 'get',
success: function (res) {

},

error: function () {
alert('error')
}
});
this.disabled = true;
});
JS;
$this->registerJs($script, yii\web\View::POS_READY);