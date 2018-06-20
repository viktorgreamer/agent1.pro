<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AgentPro */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Agent Pros', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="agent-pro-view">

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
            'status',
            'id_error',
            'time:datetime',
            'status_parsingsync',
            'status_detailed_parsing',
            'status_processing',
            'status_parsing_avito_phones',
            'status_analizing',
            'status_parsing_new',
            'status_sync',
            'status_geocogetion',
        ],
    ]) ?>

</div>
