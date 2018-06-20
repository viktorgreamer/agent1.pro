<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Parsing */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Parsings', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="parsing-view">

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'original_date',
            'count_of_views',
            'date_start',
            'rooms_count',
            'title',
            'price',
            'phone1',
            'city',
            'address',
            'house_type',
            'coords_x',
            'coords_y',
            'id_address',
            'year',
            'locality',
            'description:ntext',
            'floor',
            'floorcount',
            'id_sources',
            'grossarea',
            'kitchen_area',
            'living_area',
            'images:ntext',
            'url:ntext',
            'status_unique_phone',
            'load_analized',
            'status_unique_date',
            'status_blacklist2',
            'person',
            'id_irr_duplicate:ntext',
            'id_in_source',
            'geocodated',
            'processed',
            'broken',
            'average_price',
            'average_price_count',
            'average_price_address',
            'average_price_address_count',
            'average_price_same',
            'average_price_same_count',
            'radius',
            'tags:ntext',
            'date_of_check',
            'disactive',
            'living_area_if_rooms',
            'street',
            'is_balcon',
        ],
    ]) ?>

</div>
