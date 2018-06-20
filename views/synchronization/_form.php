<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Synchronization */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="synchronization-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id_sources')->dropDownList(
        ['1' => 'irr.ru',
            '2' => 'yandex.ru',
            '3' => 'avito.ru',
            '4' => 'youla.io',
            '5' => 'cian.ru'
        ]) ?>

    <?= $form->field($model, 'id_in_source')->textInput() ?>

    <?= $form->field($model, 'url')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'disactive')->dropDownList(
        ['1' => 'irr.ru',
            '2' => 'yandex.ru',
            '2' => 'avito.ru',
            '4' => 'youla.io',
            '5' => 'cian.ru'
        ]) ?>
    <?= $form->field($model, 'price')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
