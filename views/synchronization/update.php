<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Synchronization */

$this->title = 'Update Synchronization: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Synchronizations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="synchronization-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
