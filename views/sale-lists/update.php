<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SaleLists */

$this->title = 'Update Sale Lists: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Sale Lists', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sale-lists-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
