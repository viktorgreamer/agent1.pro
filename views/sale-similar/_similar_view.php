<?php

/* @var $this yii\web\View */

/* @var $model app\models\SaleSimilar */

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\TagsWidgets;
use app\models\Tags;
use app\models\SaleSimilar;

?>


<div class="row">

    <div class="col-sm-1">
        <? info($model->id); ?>
    </div>
    <div class="col-sm-6">
        <div class="row">
            <? echo span($model->similar_ids); ?>
        </div>
        <div class="row">
            <? echo  span($model->similar_ids_all); ?>
        </div>


    </div>

    <div class="col-sm-4">
        <? echo TagsWidgets::widget(['tags' => Tags::convertToArray($model->tags_id)]); ?>
        <? info("ID_SOURCES"); ?>

        <?php foreach (explode(",",$model->id_sources) as $id_source) {
            echo \app\models\Renders::renderSources($id_source);
        }
      ?>

    </div>
    <div class="col-sm-1">
        <? echo " СТАТУС " . span(SaleSimilar::DISACTIVE_STATUSES[$model->status]); ?>
        <? echo " СТАТУС МОДЕРАЦИИ " . span(SaleSimilar::MODERATION_STATUSES[$model->moderated]); ?>
    </div>

</div>
<? info('Similar_sales'); ?>
<? $similar_sales = \app\models\Sale::find()->where(['in', 'id', \app\models\Tags::convertToArray($model->similar_ids)])->all();
echo $this->render('@app/views/sale/_mini_sale_similar', ['sales' => $similar_sales, 'contacts' => true]); ?>

<? info('Similar_sales_ALL'); ?>

<? $similar_sales = \app\models\Sale::find()->where(['in', 'id', \app\models\Tags::convertToArray($model->similar_ids_all)])->all();
echo $this->render('@app/views/sale/_mini_sale_similar', ['sales' => $similar_sales, 'contacts' => true]); ?>


