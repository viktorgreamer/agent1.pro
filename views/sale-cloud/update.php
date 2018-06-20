<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Parsing */

$this->title = 'Update Parsing: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Parsings', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="parsing-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
