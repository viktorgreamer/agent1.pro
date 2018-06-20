<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\SmsApi */

$this->title = 'Create Sms Api';
$this->params['breadcrumbs'][] = ['label' => 'Sms Apis', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-api-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
