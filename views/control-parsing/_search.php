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
        <div class="col-lg-7">
            <?= Mdb::ActiveSelect($model, 'type',Control::mapTypesControls(), ['multiple' => true]); ?>
        </div>
        <div class="col-lg-3">
            <?= Mdb::ActiveSelect($model, 'status', ControlParsing::STATUS, ['multiple' => true]); ?>
        </div> <div class="col-lg-3">
            <?= Mdb::ActiveSelect($model, 'server', [ 0 => 'ANY'] + \yii\helpers\ArrayHelper::map(ControlParsing::find()->select('server')->distinct()->all(),'server','server')); ?>
        </div>
        <div class="col-lg-2">
            <?= Html::submitButton(Yii::t('app', ICON_SEARCH), ['class' => 'btn btn-primary btn-sm btn-rounded']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
