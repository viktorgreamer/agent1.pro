<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgentProSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agent-pro-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'status') ?>

    <?= $form->field($model, 'id_error') ?>

    <?= $form->field($model, 'time') ?>

    <?= $form->field($model, 'status_parsingsync') ?>

    <?php // echo $form->field($model, 'status_detailed_parsing') ?>

    <?php // echo $form->field($model, 'status_processing') ?>

    <?php // echo $form->field($model, 'status_parsing_avito_phones') ?>

    <?php // echo $form->field($model, 'status_analizing') ?>

    <?php // echo $form->field($model, 'status_parsing_new') ?>

    <?php // echo $form->field($model, 'status_sync') ?>

    <?php // echo $form->field($model, 'status_geocogetion') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
