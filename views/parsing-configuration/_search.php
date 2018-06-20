<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mdb;

/* @var $this yii\web\View */
/* @var $model app\models\ParsingConfigurationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parsing-configuration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'id_sources',
                'placeholder' => 'ресурс',
                'options' => [10 => 'любой'] + \app\models\Sale::ID_SOURCES,
                'label' => 'ресурс',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-sm-3"> <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'active',
                'placeholder' => 'Статус',
                'options' => [10 => 'любой'] + \app\models\ParsingConfiguration::STATUS,
                'label' => 'Статус',
                'color' => 'primary'
            ]); ?>

        </div>
        <div class="col-sm-3"> <?php echo Mdb::ActiveSelect($model, 'module_id', [10 => 'любой'] + \app\models\ParsingConfiguration::getModules()); ?>


        </div>
        <div class="col-sm-3"> <?php echo Mdb::ActiveCheckbox($model, 'ready', ['label' => 'READY']); ?>


        </div>




        <div class="col-sm-4">
            <div class="row">
                <div class="col-sm-6">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                </div>
                <div class="col-sm-6">
                    <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
                </div>
            </div>


        </div>

        <?php ActiveForm::end(); ?>
    </div>
</div>
