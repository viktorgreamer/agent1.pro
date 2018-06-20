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


$sale = $model;
// инифиализация
$tags = $sale->tags;
if ($sale->addresses) $boolAddress = true;
if ($sale) {


    ?>

    <div class="container-fluid">

        <? if ($_SESSION['moderated_mode']) echo $this->render('_moderate_table', ['sale' => $sale]); ?>

        <?php   if ($sale->similarNew->status == 3) info('ПРОДАНО'); ?>
        <div class="row no-p sim_id_<?php echo $sale->id_similar; ?>"
             id="row_<?= $sale->id; ?>" <?php if ($sale->similarNew->moderated == 3) echo "style=\"background-color: #e8f5e9;\""; ?>>
            <div class="col-sm-1"><?= $sale->renderUrl(); ?>
                <div class="row" style='min-height: 90px;padding-right: 20px;'>
                    <? echo $sale->photos; ?>
                </div>
            </div>


            <div class="col-sm-2">
                <strong> <?php echo Sale::ROOMS_COUNT_ARRAY[$sale->rooms_count] ?></strong>
                <br>
                <?php echo $sale->renderAddress(); ?>

                <?php

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
                    <?php echo $sale->renderAreas(); ?>
                    <br> <?php echo \app\models\Renders::Days_ago($sale->date_start); ?>
                </small>
                <?php if ($boolAddress) { ?>
                    <a id="#AddTagsAddress<?php echo $sale->id_address; ?>" type="button"
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

                <a id="#AddTags<?= $sale->id ?>" type="button"
                   data-toggle="modal"
                   data-target="#TagsModal<?= $sale->id ?>"> <i class="fa fa-tags blue-text fa-2x"
                                                                aria-hidden="true"></i>
                </a>


                <div class="modal fade" id="TagsModal<?= $sale->id ?>" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body">

                                <? echo $this->render('//tags/quick-add-form', [
                                    'parent_id' => $sale->id,
                                    'realtags' => $sale->tagsSale,
                                    'type' => 'sale',
                                    'id_address' => $boolAddress

                                ]); ?>


                            </div>
                        </div>
                    </div>
                </div>

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

                            // if (in_array('show_stat', explode(",", $options))) {
                            echo "<br>" . $sale->renderStat();
                            $title_to_copy = strip_tags($sale->title_to_copy_all);
                            $current = $_SESSION['title_to_copy'];
                            $current .= $title_to_copy . " \n ";
                            $_SESSION['title_to_copy'] = $current;
                            ?>
                            <textarea id="id<?= $sale->id; ?>" hidden><?php echo strip_tags($sale->title_to_copy); ?> </textarea>


                        </small>
                    </div>
                    <div class="col-sm-7">
                        <?= $sale->renderContacts(); ?>
                    </div>
                </div>
            </div>


            <div class="col-sm-1">

                <?php if ($controls) {
                    echo $this->render('_sale_controls1', compact(['sale', 'salefilter', 'salelist']));
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




