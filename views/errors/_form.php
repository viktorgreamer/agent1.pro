<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Errors */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="errors-form">

        <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-lg-2">
            <?= $form->field($model, 'id')->textInput(); ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'fatality')->dropDownList(\app\models\Errors::Fatalities(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-1">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
        <?php ActiveForm::end(); ?>


</div>
