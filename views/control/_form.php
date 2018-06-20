<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Control */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="control-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'fed_okrug')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'type_of_region')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region_rus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'oblast_rus')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_step')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'is_analized')->textInput() ?>

    <?= $form->field($model, 'coords_x')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coords_y')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'log')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'info_array')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
