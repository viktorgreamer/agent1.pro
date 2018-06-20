<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SeachingParsingControl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parsing-control-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'date_start') ?>

    <?= $form->field($model, 'date_finish') ?>

    <?= $form->field($model, 'type') ?>

    <?= $form->field($model, 'id_sources') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'config_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
