<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ParsingConfiguration */

$this->title = 'Update Parsing Configuration: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Parsing Configurations', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="parsing-configuration-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
