<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\SmsApi */

$this->title = 'Update Sms Api: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Sms Apis', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="sms-api-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
