<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WdCookies */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wd-cookies-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ip_port')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'id_server')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'body')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'time')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
