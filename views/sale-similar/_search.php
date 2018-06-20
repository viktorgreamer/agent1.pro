<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\Models\SaleSimilarSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-similar-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'similar_ids') ?>

    <?= $form->field($model, 'tags_id') ?>

    <?= $form->field($model, 'price_up') ?>

    <?= $form->field($model, 'price_down') ?>

    <?php // echo $form->field($model, 'similar_ids_all') ?>

    <?php // echo $form->field($model, 'moderated') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'debug_status') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
