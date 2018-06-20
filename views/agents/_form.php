<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Agents */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agents-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'date')->textInput() ?>

    <?= $form->field($model, 'type')->textInput() ?>

    <?= $form->field($model, 'count_ads')->textInput() ?>

    <?= $form->field($model, 'person')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'avito_count')->textInput() ?>

    <?= $form->field($model, 'irr_count')->textInput() ?>

    <?= $form->field($model, 'yandex_count')->textInput() ?>

    <?= $form->field($model, 'cian_count')->textInput() ?>

    <?= $form->field($model, 'youla_count')->textInput() ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'person_type')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
