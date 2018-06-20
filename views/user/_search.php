<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UserSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'user_id') ?>

    <?= $form->field($model, 'network') ?>

    <?= $form->field($model, 'identity') ?>

    <?= $form->field($model, 'first_name') ?>

    <?= $form->field($model, 'last_name') ?>

    <?php // echo $form->field($model, 'email') ?>

    <?php // echo $form->field($model, 'phone') ?>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'auth_date') ?>

    <?php // echo $form->field($model, 'test_date') ?>

    <?php // echo $form->field($model, 'exp_date') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'extra') ?>

    <?php // echo $form->field($model, 'rent') ?>

    <?php // echo $form->field($model, 'sale') ?>

    <?php // echo $form->field($model, 'city_modules') ?>

    <?php // echo $form->field($model, 'semysms_token') ?>

    <?php // echo $form->field($model, 'vk_token') ?>

    <?php // echo $form->field($model, 'list_or_vk_groups') ?>

    <?php // echo $form->field($model, 'money') ?>

    <?php // echo $form->field($model, 'irr_id_partners') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
