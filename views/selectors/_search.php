<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Sources;
/* @var $this yii\web\View */
/* @var $model app\models\SelectorsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="selectors-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">

        <div class="col-lg-2">
            <?= $form->field($model, 'id_sources')->dropDownList([0=> 'ANY'] + Sources::getMap(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'type')->dropDownList([0=> 'ANY'] + \app\models\Selectors::getTypes(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'id_error')->dropDownList([0=> 'ANY'] + \app\models\Errors::getMap(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'count')->dropDownList([0=> 'ANY'] + \app\models\Selectors::getCounts(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-2">
            <?= $form->field($model, 'id_parent')->dropDownList([10000 => 'ANY',0 => 'NO'] + \app\models\Selectors::getParents(), ['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'pattern')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'alias')->textInput(['maxlength' => true]) ?>
        </div>

        <?php // echo $form->field($model, 'id_error') ?>

        <div class="form-group">
            <?= Html::submitButton(ICON_SEARCH, ['class' => CLASS_BUTTON]) ?>

        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
