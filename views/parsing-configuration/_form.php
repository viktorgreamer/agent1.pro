<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ParsingConfiguration */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parsing-configuration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'start_link')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_ip')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_timestamp')->textInput() ?>
    <?= $form->field($model, 'last_timestamp_new')->textInput() ?>
    <?= $form->field($model, 'non_start_link')->textInput() ?>
    <?= $form->field($model, 'success_stop')->textInput() ?>
    <?= $form->field($model, 'active')->dropDownList(\app\models\ParsingConfiguration::STATUS) ?>
    <div class="md-form form-sm">
        <?php // выбор периода подачит обьявлений
        echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'value' => $model->active,
            'name' => 'active',
            'placeholder' => 'статус',
            'options' => \app\models\ParsingConfiguration::STATUS,
            'label' => '',
            'color' => 'primary'
        ]);
        ?>
    </div> <div class="md-form form-sm">
        <?php // выбор периода подачит обьявлений
        echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'value' => $model->id_sources,
            'name' => 'id_sources',
            'placeholder' => 'id_sources',
            'options' => \app\models\Sale::ID_SOURCES,
            'label' => '',
            'color' => 'primary'
        ]);
        ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
