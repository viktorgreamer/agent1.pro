<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\components\SaleFilterWidgets;

/* @var $this yii\web\View */
/* @var $model app\models\SaleFilters */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sale Filters', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sale-filters-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <!-- --><? /* echo  DetailView::widget([
        'model' => $model,
        'attributes' => [
         //   'id',
            'user_id',
         //   'period_ads',
            'name',
            'rooms_count',
         //   'house_type',
            'locality',
         //   'district',
            'text_like',
            'polygon_text:ntext',
            'price_down',
            'price_up',
            'grossarea_down',
            'grossarea_up',
            'status_blacklist2',
            'agents',
            'housekeepers',
            'date_of_ads',
            'floor_down',
            'floor_up',
            'floorcount_down',
            'floorcount_up',
            'not_last_floor',
            'sort_by',
          //  'black_list_id:ntext',
          //  'white_list_id:ntext',
          //  'mail_inform',
          //  'sms_inform',
           // 'is_super_filter',
            'discount',
          //  'date_start',
           // 'date_finish',
            'phone:ntext',
            'year_up',
            'year_down',
           // 'id_sources',
            'id_address:ntext',
            'is_client',
            'komment:ntext',
           // 'tags_id',
        ],
    ]) */ ?>
    <?php
    if ($model->type == 1) {
        echo SaleFilterWidgets::widget([
            'salefilter' => $model,
            'type' => 'client']);
    } else  echo  SaleFilterWidgets::widget([
        'salefilter' => $model,
        'type' => 'salefilter']);

    ?>


</div>
