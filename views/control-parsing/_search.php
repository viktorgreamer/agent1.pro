<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mdb;
use app\models\Control;
use app\models\ControlParsing;


/* @var $this yii\web\View */
/* @var $model app\models\ControlParsingSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="control-parsing-search">
    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="md-form">
                <?= Mdb::ActiveSelect($model, 'type', array_combine(Control::SECTIONS_OF_CONTROL, Control::SECTIONS_OF_CONTROL), ['multiple' => true]); ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-5">
            <div class="md-form">
                <?= Mdb::ActiveSelect($model, 'status', ControlParsing::STATUS, ['multiple' => true]); ?>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="md-form">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary btn-sm btn-rounded']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
