<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Sources;

/* @var $this yii\web\View */
/* @var $model app\models\Selectors */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="selectors-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">

        <div class="col-lg-2">
            <?= $form->field($model, 'id_sources')->dropDownList(Sources::getMap(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'type')->dropDownList(\app\models\Selectors::getTypes(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'id_error')->dropDownList(\app\models\Errors::getMap(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'count')->dropDownList(\app\models\Selectors::getCounts(), ['class' => 'mdb-select']) ?>
        </div> <div class="col-lg-4">
            <?= $form->field($model, 'id_parent')->dropDownList([0 => 'NO' ] + \app\models\Selectors::getParents(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'pattern')->textarea(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>
    <div class="col-lg-3">
      <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
