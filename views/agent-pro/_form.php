<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AgentPro */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="agent-pro-form">


        <?php $form = ActiveForm::begin(); ?>
         <div class="row">

        <div class="col-lg-3">
            <?= $form->field($model, 'status')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_parsingsync')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_detailed_parsing')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_processing')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_parsing_avito_phones')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_analizing')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_parsing_new')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_sync')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'status_geocogetion')->dropDownList(\app\models\AgentPro::mapStatuses(),['class' => 'mdb-select']) ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'page_limit')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'period_check')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'period_check_new')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <?= $form->field($model, 'id_sources')->textInput() ?>
        </div>
        <div class="col-lg-3">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
