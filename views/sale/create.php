<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form ActiveForm */
?>
<div class="sale-create">

    <?php $form = ActiveForm::begin(); ?>

       <!-- <?/*= $form->field($model, 'id') */?>
        --><?/*= $form->field($model, 'date_start') */?>
        <?= $form->field($model, 'rooms_count') ?>
    <!--    --><?/*= $form->field($model, 'price') */?>
       <!-- --><?/*= $form->field($model, 'id_address') */?>
<!--        --><?//= $form->field($model, 'house_type') ?>
        <?= $form->field($model, 'floor') ?>
     <!--   --><?/*= $form->field($model, 'floorcount') */?>
<!--        --><?//= $form->field($model, 'id_sources') ?>
        <?= $form->field($model, 'grossarea') ?>
     <!--   <?/*= $form->field($model, 'status_unique_phone') */?>
        <?/*= $form->field($model, 'year') */?>
        <?/*= $form->field($model, 'load_analized') */?>
        <?/*= $form->field($model, 'status_unique_date') */?>
        <?/*= $form->field($model, 'status_blacklist2') */?>
        <?/*= $form->field($model, 'average_price') */?>
        <?/*= $form->field($model, 'average_price_count') */?>
        <?/*= $form->field($model, 'average_price_address') */?>
        <?/*= $form->field($model, 'average_price_address_count') */?>
        <?/*= $form->field($model, 'average_price_same') */?>
        <?/*= $form->field($model, 'average_price_same_count') */?>
        <?/*= $form->field($model, 'coords_x') */?>
        <?/*= $form->field($model, 'coords_y') */?>
        <?/*= $form->field($model, 'description') */?>
        <?/*= $form->field($model, 'images') */?>
        <?/*= $form->field($model, 'url') */?>
        <?/*= $form->field($model, 'person') */?>
        <?/*= $form->field($model, 'id_irr_duplicate') */?>
        <?/*= $form->field($model, 'title') */?>
        <?/*= $form->field($model, 'phone1') */?>
        <?/*= $form->field($model, 'city') */?>
    -->    <?= $form->field($model, 'address') ?>
        <?= $form->field($model, 'locality') ?>
    
        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- sale-create -->
