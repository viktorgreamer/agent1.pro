<?php

/* @var $model \app\models\SaleFilters */

use app\models\ParsingExtractionMethods;
use app\models\Tags;
use app\components\TagsWidgets;
use app\models\Actions;
if (!empty($model->rooms_count)) $body = " Куплю " . \app\models\RenderSalefilters::RoomsCount($model->rooms_count);

if ($model->price_up) $body .= ", цена до <b>" . $model->price_up . " </b>";
if ($model->grossarea_down) $body .= ", площадью от <b>" . $model->grossarea_down . " </b> м2";
if ($model->year_down) $body .= ",дом нестарше <b>" . $model->year_down . " </b> г.п.";

$body .= "<br>" . $model->komment;
$body .= "<br><div style='background-color: #b0bec5'>" . $model->hidden_comment . "</div> ";
$phone = ParsingExtractionMethods::findPhone($model->hidden_comment);
?>

<div class="card">
    <div class="card-header primary-color white-text">
        <?php echo yii\helpers\Html::a(\app\components\Mdb::Fa('search fa-fw text-white'), ['sale/search-by-filter', 'id' => $model->id], ['target' => '_blank']); ?>
        &nbsp

        <?php if ($model->status != \app\models\SaleFilters::STATUS_DISABLED) echo Actions::renderChangeStatus($model->id,Actions::SALEFILTER,Actions::SALEFILTER_STATUS, \app\models\SaleFilters::STATUS_DISABLED,\app\components\Mdb::Fa('pause  fa-fw text-warning')); ?>
        &nbsp
        <?php if ($model->status != \app\models\SaleFilters::STATUS_DELETED) echo Actions::renderChangeStatus($model->id,Actions::SALEFILTER,Actions::SALEFILTER_STATUS, \app\models\SaleFilters::STATUS_DELETED,\app\components\Mdb::Fa('trash fa-fw  text-danger')); ?>
        &nbsp
        <?php if ($model->status != \app\models\SaleFilters::STATUS_ACTIVE) echo Actions::renderChangeStatus($model->id,Actions::SALEFILTER,Actions::SALEFILTER_STATUS, \app\models\SaleFilters::STATUS_ACTIVE,\app\components\Mdb::Fa('play fa-fw  green-text')); ?>
        &nbsp
        <?php echo \yii\helpers\Html::a(\app\components\Mdb::Fa('edit fa-fw text-white'), ['sale/index2', 'id' => $model->id], ['target' => '_blank']); ?>
        &nbsp

        <?php if (Yii::$app->user->can('admin')) echo \app\widgets\SaleFilterImage::widget(['id' => $model->id]); ?>

        &nbsp&nbsp

        <?php echo $model->name; ?>


        <div class=" float-right white-text">
            <a data-toggle="collapse" href="#collapseFilter<?= $model->id; ?>" aria-expanded="false"
               aria-controls="collapseFilter<?= $model->id; ?>">
                <div class="white-text"><i class="fa fa-angle-down">Развернуть</i></div>
            </a>
        </div>

    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-lg-10 col-md-10 col-sm-10 col-10">
                <?= $body; ?>
            </div>
            <div class="col-lg-2 col-md-2 col-sm-6 col-6">
                <?= " до  " . \app\models\Renders::Price($model->price_up); ?>
            </div>
            <div class="float-right">

            </div>
            <!-- / Collapse buttons -->


            <div class="collapse" id="collapseFilter<?= $model->id; ?>">
                <div class="mt-3">
                    <div class="col-lg-12 col-md-12 col-sm-12 col-12">
                        <p class="h5-responsive text-success"><i class="fa fa-check" aria-hidden="true"></i>
                            РАССМАТРИВАЕМ
                        </p>
                        <blockquote class="blockquote bq-success">
                            <? echo TagsWidgets::widget(['tags' => Tags::convertToArray($model->plus_tags)]); ?>
                        </blockquote>

                        <p class="h5-responsive text-danger"><i class="fa fa-ban" aria-hidden="true"></i> НЕ
                            РАССМАТРИВАЕМ
                        </p>
                        <blockquote class="blockquote bq-danger">
                            <? echo TagsWidgets::widget(['tags' => Tags::convertToArray($model->minus_tags)]); ?>
                        </blockquote>
                        <?php if ($phone) { ?>
                            <a href="tel:<?= $phone; ?>"
                               class="btn btn-primary btn-rounded btn-sm"><?= ICON_PHONE . " " . $phone; ?></a>
                        <? } ?>

                        <a id="#AddTagsSalefilterButton<?php echo $model->id ?>" type="button"
                           data-toggle="modal"
                           data-target="#AddTagsSalefilter<?php echo $model->id ?>"> <i
                                    class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
                        </a>
                        <div class="modal fade" id="AddTagsSalefilter<?php echo $model->id ?>" tabindex="-1"
                             role="dialog"
                             aria-labelledby="AddTagsSalefilterButton" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">
                                        <? echo $this->render('//tags/quick-add-form', [
                                            'parent_id' => $model->id,
                                            'realtags' => $model->getTags(),
                                            'type' => 'salefilter'
                                        ]);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-danger float-right"><?= Yii::$app->formatter->asRelativeTime($model->time); ?></div>
    </div>


</div>

