<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SeachingParsing */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parsing-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>




    <?= $form->field($model, 'rooms_count') ?>

    <?php // echo $form->field($model, 'title') ?>

    <?php // echo $form->field($model, 'price') ?>

    <?php // echo $form->field($model, 'phone1') ?>

    <?php // echo $form->field($model, 'city') ?>

    <?php // echo $form->field($model, 'address') ?>

    <?php // echo $form->field($model, 'house_type') ?>

    <?php // echo $form->field($model, 'coords_x') ?>

    <?php // echo $form->field($model, 'coords_y') ?>

    <?php // echo $form->field($model, 'id_address') ?>

    <?php // echo $form->field($model, 'year') ?>

    <?php // echo $form->field($model, 'locality') ?>

    <?php // echo $form->field($model, 'description') ?>

    <?php // echo $form->field($model, 'floor') ?>

    <?php // echo $form->field($model, 'floorcount') ?>

    <?php  echo $form->field($model, 'id_sources') ?>

    <?php // echo $form->field($model, 'grossarea') ?>

    <?php // echo $form->field($model, 'kitchen_area') ?>

    <?php // echo $form->field($model, 'living_area') ?>

    <?php // echo $form->field($model, 'images') ?>

    <?php // echo $form->field($model, 'url') ?>

    <?php // echo $form->field($model, 'status_unique_phone') ?>

    <?php // echo $form->field($model, 'load_analized') ?>

    <?php // echo $form->field($model, 'status_unique_date') ?>

    <?php // echo $form->field($model, 'status_blacklist2') ?>

    <?php // echo $form->field($model, 'person') ?>

    <?php // echo $form->field($model, 'id_irr_duplicate') ?>

    <?php // echo $form->field($model, 'id_in_source') ?>

    <?php // echo $form->field($model, 'geocodated') ?>

    <?php // echo $form->field($model, 'processed') ?>

    <?php // echo $form->field($model, 'broken') ?>

    <?php // echo $form->field($model, 'average_price') ?>

    <?php // echo $form->field($model, 'average_price_count') ?>

    <?php // echo $form->field($model, 'average_price_address') ?>

    <?php // echo $form->field($model, 'average_price_address_count') ?>

    <?php // echo $form->field($model, 'average_price_same') ?>

    <?php // echo $form->field($model, 'average_price_same_count') ?>

    <?php // echo $form->field($model, 'radius') ?>

    <?php // echo $form->field($model, 'tags') ?>

    <?php // echo $form->field($model, 'date_of_check') ?>

    <?php  echo $form->field($model, 'disactive') ?>

    <?php // echo $form->field($model, 'living_area_if_rooms') ?>

    <?php // echo $form->field($model, 'street') ?>

    <?php // echo $form->field($model, 'is_balcon') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
