<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SaleSimilar */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Sale Similar',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Sale Similars'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="sale-similar-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
