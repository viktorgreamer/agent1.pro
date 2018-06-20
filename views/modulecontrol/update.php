<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Modulecontrol */

$this->title = 'Update Modulecontrol: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Modulecontrols', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="modulecontrol-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
