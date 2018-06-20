<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Actions;
use app\components\Mdb;
use app\models\SaleSimilar;
use app\models\Sale;
use app\models\SaleFilters;
use app\models\Geocodetion;


/* @var $sale app\models\Sale */
/* @var $salefilter app\models\SaleFilters */
/* @var $salelist app\models\SaleLists */
?>
<? // блок критической информации модерация, установка адреса, что вариант продан?>
    <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle px-3" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-icons2">

            <?php if ($sale->id_similar) { ?>
                <?
                // ставим что SIMILAR MODERATED
                echo Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATED,
                    Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED), ['class' => 'dropdown-item']);
                // ставим что SIMILAR MODERATION_ONCALL
                echo Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATED, SaleSimilar::MODERATION_ONCALL,
                    Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_MODERATION_ONCALL), ['class' => 'dropdown-item']);
                ?>
            <?php } else {
                // ставим что SALE MODERATED
                echo Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_MODERATED, SaleSimilar::MODERATED,
                    Actions::getIcons(Actions::SALE, Actions::SALE_MODERATED), ['class' => 'dropdown-item']);
                // ставим что SALE MODERATION_ONCALL
                echo Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_MODERATED, SaleSimilar::MODERATION_ONCALL,
                    Actions::getIcons(Actions::SALE, Actions::SALE_MODERATION_ONCALL), ['class' => 'dropdown-item']);
            } ?>
            <a class="dropdown-item" id="#AddmanualAddress<?php echo $sale->id; ?>" type="button"
               data-toggle="modal"
               data-target="#manualAddress<?= $sale->id; ?>"><i class="fa fa-location-arrow fa-2x" aria-hidden="true"
                                                                title="Установить адрес"></i>
            </a>
            <?
            // ставим что SIMILAR MODERATED
            echo Actions::renderChangeStatus($sale->id, Actions::SALE, Actions::SALE_GEOCODATED, Geocodetion::ERROR,
                Actions::getIcons(Actions::SALE, Actions::SALE_GEOCODATED), ['class' => 'dropdown-item']); ?>
            <a class="dropdown-item change-statuses" data-id="<?= $sale->id; ?>" data-model='sale'
               data-attrname='geocodated' data-value=9><span class="fa-stack fa-lg">
  <i class="fa fa-map fa-stack-1x"></i>
  <i class="fa fa-ban fa-stack-2x text-danger"></i>
</span></a>
            <?php
            echo Actions::renderChangeStatus($sale->id_similar, Actions::SALESIMILAR, Actions::SALESIMILAR_STATUS, Sale::MAN_SOLD,
                Actions::getIcons(Actions::SALESIMILAR, Actions::SALESIMILAR_STATUS_SOLD), ['class' => 'dropdown-item']);
            ?>

            <?php if ($sale->agent->person_type == 0) echo Actions::renderChangeStatus($sale->agent->id, Actions::AGENT, Actions::AGENT_PERSON_TYPE,
                Actions::AGENT_PERSON_TYPE_AGENT, Actions::getIcons(Actions::AGENT, Actions::AGENT_PERSON_TYPE_AGENT),  ['class' => 'dropdown-item']);

            else echo Actions::renderChangeStatus($sale->agent->id, Actions::AGENT, Actions::AGENT_PERSON_TYPE,
                Actions::AGENT_PERSON_TYPE_HOUSEKEEPER, Actions::getIcons(Actions::AGENT, Actions::AGENT_PERSON_TYPE_HOUSEKEEPER),  ['class' => 'dropdown-item']);
            ?>

            <!--            <a class="dropdown-item change-statuses" data-id="--><?//= $sale->phone1; ?><!--" data-model='agent'-->
            <!--               data-attrname='person_type' data-value=1-->
            <!--            ><i class="fa fa-user-secret fa-2x" aria-hidden="true" title="Агент"></i></a>-->
        </div>
    </div>
    <div class="btn-group">
        <button type="button" class="btn btn-success dropdown-toggle px-3"
                data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-icons">

            <a class="dropdown-item" onclick="copyToClipboard('#id<?= $sale->id; ?>')"><i
                    class="fa fa-clipboard fa-2x"
                    aria-hidden="true"></i> Copy</a>

            <a class="dropdown-item add-object-to-favourites"
               data-id_item="<?= $sale->id ?>"> <i
                    class="fa fa-plus fa-2x"
                    aria-hidden="true"></i> Add</a>

        </div>
    </div>
    <!-- здесь выводятся кнопки упраления добавления или удаления в опредлеленные списки-->
    <div class="btn-group">
        <button type="button" class="btn btn-indigo dropdown-toggle px-3" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-icons">
            <?php ?>
            <?php
            // если создан salefilter
            if ($salefilter->id != 0) { ?>

                <? //
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
                foreach ($configs as $config) {
                    $is_active = preg_match("/," . $sale[$config['id']] . ",/", $salefilter[Actions::getNameAttribute(Actions::SALEFILTER, $config['attr'])], $output);
                    echo Actions::renderToggleLists($salefilter->id, Actions::SALEFILTER, $config['attr'], $sale[$config['id']],
                        Actions::getIcons(Actions::SALEFILTER, $config['attr'], $is_active), ['class' => 'dropdown-item']);
                }
                ?>

                <? if ($sale->id_similar) { ?>
                    <a class="dropdown-item on-control"
                       data-id="<?= $salefilter->id ?>"
                       data-id_item="<?= $sale->id_similar ?>"
                       data-price="<?= $sale->price ?>">
                        <i class="fa fa-exclamation red-text fa-2x" aria-hidden="true"></i><i
                            class="fa fa-eye fa-2x" aria-hidden="true" title="Следить за ценой"></i>
                    </a>
                <? } ?>

            <? } ?>

            <?php
            // если создан salefilter
            if ($salelist->id != 0) { ?>
                <a class="dropdown-item salelist-del-button"
                   data-id="<?= $salelist->id ?>"
                   data-id_item="<?= $sale->id ?>"><i
                        class="fa fa-minus-circle red-text fa-2x"
                        aria-hidden="true"></i></a>
                <a class="dropdown-item salelist-ok-button"
                   data-id="<?= $salelist->id ?>"
                   data-id_item="<?= $sale->id ?>"><i class="fa fa-check fa-2x"
                                                      aria-hidden="true"></i></a>
                <a class="dropdown-item salelist-ban-button"
                   data-id="<?= $salelist->id ?>"
                   data-id_item="<?= $sale->id ?>"><i class="fa fa-ban fa-2x"
                                                      aria-hidden="true"></i></a>
                <a class="dropdown-item list-address-del-button"
                   data-id="<?= $salelist->id ?>"
                   data-id_address="<?= $sale->id_address ?>"><i class="fa fa-remove fa-2x"
                                                                 aria-hidden="true">Удалить
                        Адрес</i>
                </a>
            <?php } ?>

        </div>
    </div>

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
/**
 * Created by PhpStorm.
 * User: User
 * Date: 14.05.2018
 * Time: 11:07
 */