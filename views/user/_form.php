<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true])->label('') ?>

    <?= $form->field($model, 'last_name')->textInput(['maxlength' => true])->label('')  ?><br>

    <?= $form->field($model, 'email')->textInput(['maxlength' => true])->label('')  ?>

    <?= $form->field($model, 'phone')->textInput(['maxlength' => 200])->label('')  ?><br>
    Будьте внимательны первый телефон будет использоваться для рассылок смс <br>



    <?= $form->field($model, 'semysms_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'vk_token')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'list_or_vk_groups')->textarea(['rows' => 6])->label('')  ?>
    <?= $form->field($model, 'irr_id_partners')->textarea(['rows' => 10])->label('irr_id_partners')  ?>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
