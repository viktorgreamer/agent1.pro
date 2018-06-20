<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Synchronization */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Synchronizations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="synchronization-view">

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
            [
                'label' => 'Источник',
                'format' => 'raw',
                'value' => function ($model) {
                    return " <a href='" . $model->url . "' target='_blank'> " . \app\models\Sale::ID_SOURCES[$model->id_sources] . "</a> <br> id=" . $model->id_in_source;
                }
            ],
            'id_in_source',
            'url:ntext',
            'title',
            'address',
            'disactive',
            'price',
        ],
    ]) ?>

</div>
