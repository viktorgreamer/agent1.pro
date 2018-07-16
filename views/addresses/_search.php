<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use app\models\Actions;
use app\components\Mdb;
use app\models\Addresses;

/* @var $this yii\web\View */
/* @var $model app\models\AddressesSearch */
/* @var $form yii\widgets\ActiveForm */
if (!$model->status) $model->status = 10;
if (!$model->tagged) $model->tagged = 10;
$session = Yii::$app->session;
$module = $session->get('module');
// $salefilter = \app\models\SaleFilters::findOne($salefilter->id);
?>


<div class="addresses-search">
    <form method="get" id="w0">
        <input type="text" name="polygon_text" id="poly" size="40" hidden
               value="<? echo $_GET['polygon_text']; ?>">
        <input type="text" name="plus_tags" id="plus_searching_tags" size="40" hidden
               value="<? echo $_GET['plus_tags']; ?>">
        <input type="text" name="minus_tags" id="minus_searching_tags" size="40" hidden
               value="<? echo $_GET['minus_tags']; ?>">

        <? // echo $form->field($model, 'address') ?>

        <div class="row">
            <div class="col-1 col-lg-1">
                <?= Mdb::ActiveTextInput($model, 'id') ?>
            </div>
            <div class="col-lg-2">
                <?= Mdb::ActiveTextInput($model, 'locality') ?>
            </div>
            <div class="col-lg-2">
                <?= Mdb::ActiveTextInput($model, 'street') ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveTextInput($model, 'house') ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveTextInput($model, 'hull') ?>
            </div>

            <div class="col-lg-1">
                <?= Mdb::ActiveSelect($model, 'house_type', \app\models\Sale::HOUSE_TYPES) ?>

            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveSelect($model, 'floorcount_down', \app\models\Sale::getFloors()) ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveSelect($model, 'floorcount_up', \app\models\Sale::getFloors()) ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveTextInput($model, 'year_up') ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveTextInput($model, 'year_down') ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveSelect($model, 'status', \app\models\Addresses::mapStatuses()); ?>
            </div>
            <div class="col-lg-1">
                <?= Mdb::ActiveSelect($model, 'tagged', [10 => 'ANY', 1 => 'NO TAGS', 2 => 'TAGS']); ?>
            </div>

            <?php // echo $form->field($model, 'year') ?>

            <?php // echo $form->field($model, 'precision_yandex') ?>


            <div class="col-md-2 col-lg-1 col-sm-2 col-2">
                <?php
                echo \app\components\Mdb::ModalBegin([
                    'header' => '<div class="searching_tags_div"></div>',
                    'class' => 'modal-lg', 'id' => 'plus_search_by_Tags',
                    'button' => [
                        'class' => 'btn-floating cyan waves-effect waves-light',
                        'title' => "<i class=\"fa fa-tags fa-2x\" aria-hidden=\"true\"></i>"
                    ]
                ]);
                ?>

                <div class="modal-body">
                    <? echo $this->render('//tags/quick-add-form-alternative', [
                        'parent_id' => 0,
                        'realtags' => \app\models\Tags::convertToArray($model->plus_tags),
                        'type' => 'plus_search',
                        'searchable' => true,
                    ]);
                    ?>
                </div>
                <?= Mdb::ModalEnd(); ?>
            </div>
            <div class="col-md-2  col-lg-1 col-sm-2 col-2">
                <?php
                echo \app\components\Mdb::ModalBegin([
                    'header' => '<div class="searching_tags_div"></div>'
                    , 'class' => 'modal-lg', 'id' => 'minus_search_by_Tags',
                    'button' => [
                        'class' => 'btn-floating red waves-effect waves-light',
                        'title' => "<i class=\"fa fa-tags fa-2x\" aria-hidden=\"true\"></i>"
                    ]
                ]);
                ?>
                <div class="modal-body">
                    <? echo $this->render('//tags/quick-add-form-alternative', [
                        'parent_id' => 0,
                        'realtags' => \app\models\Tags::convertToArray($model->minus_tags),
                        'type' => 'minus_search',
                        'searchable' => true,
                    ]);
                    ?>
                </div>
                <?= Mdb::ModalEnd(); ?>
            </div>

            <div class="col-md-2  col-lg-2 col-sm-2 col-2">
                <a class=" btn btn-success  btn-sm tags-action-button-address-all" href="#">ADD TAGS</a>
            </div>
            <div class="col-md-2  col-lg-2 col-sm-2 col-2">
                <a id="#AddTagsAddress" type="button" href="#" data-toggle="modal" data-target="#TagsModal"> <i
                            class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
                </a>
                <div class="modal fade" id="TagsModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
                     aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <? echo $this->render('//tags/quick-add-form-alternative', [
                                    'type' => 'address',
                                    'parent_id' => 'setToAllAddresses'
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-2  col-lg-1 col-sm-2 col-2">
                <div class="form-group">
                    <?= Html::submitButton(ICON_SEARCH, ['class' => CLASS_BUTTON_SUCCESS]) ?>
                </div>
            </div>
            <div class="col-md-2  col-lg-1 col-sm-2 col-2">
                <?= Actions::renderChangeStatus(Actions::TO_MANY_ADDRESSES, Actions::ADDRESSES, Actions::ADDRESSES_BALCON, Addresses::BALCON, Html::button("Балк.",['class' => CLASS_BUTTON])); ?>
            </div>
            <div class="col-md-2  col-lg-1 col-sm-2 col-2">
                <?= Actions::renderChangeStatus(Actions::TO_MANY_ADDRESSES, Actions::ADDRESSES, Actions::ADDRESSES_BALCON, Addresses::LOGIYA, Html::button("Лодж.",['class' => CLASS_BUTTON])); ?>
            </div>
            <div class="col-md-2  col-lg-1 col-sm-2 col-2">
                <?= Actions::renderChangeStatus(Actions::TO_MANY_ADDRESSES, Actions::ADDRESSES, Actions::ADDRESSES_BALCON, Addresses::ANY, Html::button("Any",['class' => CLASS_BUTTON])); ?>
            </div>
        </div>

    </form>

    <?php
    if ($addresses) {
        $ids = [];
        foreach ($addresses as $address) {
            array_push($ids, $address->id);
        }
        $session->set('addresses', $ids);
    }
    ?>
