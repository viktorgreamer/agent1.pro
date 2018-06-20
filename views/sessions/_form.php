<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sessions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sessions-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_session')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'current_url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'date_start')->textInput() ?>

    <?= $form->field($model, 'datetime_check')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
