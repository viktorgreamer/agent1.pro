<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Renders;
use app\components\TagsWidgets;
use app\models\Methods;

/* @var $sale app\models\Sale */
/* @var $salesimilar app\models\SaleSimilar */
?>
<div class="row">


    <div class="col-sm-1">
        <?= Html::a("add=" . $sale->id_address, Url::toRoute(['/addresses/view', 'id' => $sale->id_address]), ['target' => '_blank']); ?>
    </div>
    <div class="col-sm-2">

        <?= Html::a("sim=" . $sale->id_similar, Url::toRoute(['/sale-similar/view', 'id' => $sale->id_similar]), ['target' => '_blank']); ?>
        <?= Html::a("sale" . $sale->id, Url::toRoute(['/sale/view', 'id' => $sale->id]), ['target' => '_blank']); ?>

    </div>

    <div class="col-sm-4">
        <?php $body = "<span>TAGS_SALE" . "</span><br>" . $sale->tags_id . "<br> " . TagsWidgets::widget(['tags' => Methods::convertToArrayWithBorders($sale->tags_id), 'moderate' => true]) . "<br>"; ?>
        <?php $body .= "<span>TAGS_SIMILAR" . "</span>
<br>" . $sale->similarNew->tags_id . "<br>" .
            TagsWidgets::widget(['tags' => Methods::convertToArrayWithBorders($sale->similar->tags_id), 'moderate' => true]) . "<br>"; ?>
        <?php $body .= "<span>TAGS_ADDRESS" . "</span><br>" . $sale->addresses->tags_id . "<br>" . TagsWidgets::widget(['tags' => $sale->addresses->tags, 'moderate' => true]); ?>
        <?php // $body .= " <br> " .$sale->similarNew->tags_id . " <br> " . $sale->addresses->tags_id ; ?>

        <?php echo Renders::toModal("tags", $body); ?>
    </div>


    <div class="col-sm-4">

    </div>
    <div class="col-sm-1">
    </div>

</div>