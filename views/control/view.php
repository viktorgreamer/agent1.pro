<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Control */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="control-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'fed_okrug',
            'region',
            'type_of_region',
            'region_rus',
            'oblast_rus',
            'max_step',
            'status',
            'is_analized',
            'coords_x',
            'coords_y',
            'zoom',
            'log',
            'info_array:ntext',
            'last_timestamp:datetime',
            'last_check_of_die',
            'last_check_of_lost',
            'parsing_status',
            'last_check_controlparsing',
        ],
    ]) ?>

</div>
