<?php
/* @var $model app\models\Sale */
/* @var $item app\models\Sale */

/* @var $this yii\web\View */

use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\models\SaleFilters;
use app\components\Mdb;
use app\models\Sale;


if ($_GET['type_of_show'] == '3') {
    $sale = \app\models\Sale::find()
        ->where(['rooms_count' => $model->rooms_count])
        ->andwhere(['id_address' => $model->id_address])
        ->andwhere(['floor' => $model->floor])
        ->andwhere(['between', 'grossarea',
            $model->grossarea * (100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100,
            $model->grossarea * (100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100])
        ->andwhere(['>=', 'price', $model->price])
        ->orderBy('price')
        ->one();
} elseif ($_GET['type_of_show'] == '1') {
    $sale = \app\models\Sale::find()
        ->where(['rooms_count' => $model->rooms_count])
        ->andwhere(['id_address' => $model->id_address])
        ->andwhere(['floor' => $model->floor])
        ->andwhere(['between', 'grossarea',
            $model->grossarea * (100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100,
            $model->grossarea * (100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100])
        ->orderBy('price')
        ->one();
} else $sale = $model;
// инифиализация
$tags = $sale->tags;
if ($sale->addresses) $boolAddress = true;
if ($sale) {


    ?>

    <div class="container-fluid">
        <?php if ($sale->similarNew->status == 3) info('ПРОДАНО'); ?>
        <div class="row no-p sim_id_<?php echo $sale->id_similar; ?>"
             id="row_<?= $sale->id; ?>" <?php if ($sale->similarNew->moderated == 3) echo "style=\"background-color: #e8f5e9;\""; ?>>
            <div class="col-sm-1"><?= $sale->renderUrl(); ?>
                <div class="row" style='min-height: 90px;padding-right: 20px;'>
                    <? echo $sale->photos; ?>
                </div>
            </div>


            <div class="col-sm-2">
                <strong> <?php echo \app\models\Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?></strong>
                <br>

                <?php echo $sale->renderAddress(); ?>
                <? if ($_SESSION['moderated_mode']) if ($sale->id_address) echo Html::a($sale->id_address, Url::toRoute(['addresses/view', 'id' => $sale->id_address]), ['target' => '_blank']);
                else echo "-"; ?>
                <?php
                // РЕНДЕРИМ ПОХОЖИЕ ВАРИАНТЫ
                //                if ($sale->id_address) {
                //                    $similarsales = $sale->similar;
                //                }
                if ($sale->similarNew) {
                    //  echo span("similar_id =".$sale->similarNew->id);

                    $similarsales = $sale->similarNewSales;
                }
                ?>
                <?php if ($similarsales) {
                    echo \app\components\Mdb::ModalBegin([
                        'header' => 'Похожие варианты', 'class' => '', 'id' => "sale_similar_" . $sale->id,
                        'button' => [
                            'class' => 'span.badge badge-pill light-blue',
                            'title' => count($similarsales)
                        ]
                    ]);
                    ?>
                    <div class="modal-body">
                        <?= $this->render('@app/views/sale/_mini_sale_similar', ['sales' => $similarsales, 'contacts' => true, 'controls' => true, 'salefilter' => $salefilter]); ?>
                    </div>
                    <?= Mdb::ModalEnd(); ?>
                    <br>
                <? } ?>
                <?php //  echo $sale->id_address; ?>
                <small>
                    <?php echo $sale->renderFloors();
                    echo $sale->renderHouse_type(); ?>
                    <strong><?php echo $sale->renderAreas(); ?></strong>
                    <br> <?php echo \app\models\Renders::Days_ago($sale->date_start); ?>
                </small>
                <?php if ($boolAddress) { ?>
                    <a id="#AddTagsAddress<?php echo $sale->id_address; ?>" type="button" href="#"
                       data-toggle="modal"
                       data-target="#TagsModal<?= $sale->id_address ?>"> <i
                                class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
                    </a>
                    <div class="modal fade" id="TagsModal<?php echo $sale->id_address; ?>" tabindex="-1"
                         role="dialog"
                         aria-labelledby="myModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <? echo $this->render('//tags/quick-add-form', [
                                        'parent_id' => $sale->id_address,
                                        'realtags' => $sale->addresses->getTags(),
                                        'type' => 'address',
                                        'id_address' => $boolAddress
                                    ]);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <? } ?>

            </div>
            <div class="col-sm-4">
                <a data-toggle="collapse" href="#collapseExample<?= $sale->id ?>" aria-expanded="false"
                   aria-controls="collapseExample">Описание </a>
                <div class="collapse" id="collapseExample<?= $sale->id ?>">
                    <?php echo $sale->description; ?>
                </div>
                <br>
                <!-- блок модального окна добавления tags  -->


                <?php

                //  echo \app\models\Tags::convertToString($tags);
                echo "<br>" . \app\components\TagsWidgets::widget(['tags' => array_unique($tags)]);

                ?>


            </div>

            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-5">
                        <small>
                            <?php
                            echo Html::a(\app\models\Renders::Price($sale->price), ['sale-analitics/show', 'id' => $sale->id], ['target' => '_blank']);
                            // если устаовлен флажек показывать статистику
                            ?>


                        </small>
                    </div>
                    <div class="col-sm-7">
                        <?= $sale->renderContacts(); ?>
                    </div>
                </div>
            </div>


            <div class="col-sm-1">

                <?php if ($controls) {
                    echo $this->render('_sale_controls', compact(['sale', 'salefilter', 'salelist']));
                    ?>

                    <?php
                }
                ?>


            </div>
        </div>
        <hr style="margin-top: 0px; margin-bottom: 0px;">
    </div>
    <?php
}




