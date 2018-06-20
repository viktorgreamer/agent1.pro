<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;
use app\models\SaleFilters;

/* @var $this yii\web\View */
/* @var $model app\models\SaleListsSearch */
/* @var $form yii\widgets\ActiveForm */
$session = Yii::$app->session;
?>

<div class="sale-lists-search">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'row'],
        'action' => 'index'
    ]) ?>
    <div class="col-sm-2">
        <?php echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'name' => 'user_id',
            'placeholder' => 'Агент',
            'options' => [0 => 'Любой'] + User::getAvailableUsersAsArray(),
            'label' => 'Агент',
            'color' => 'primary'
        ]); ?>
    </div>


    <div class="col-sm-2">
        <?php echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'name' => 'type',
           // 'multiple' => 'true',
            'placeholder' => 'Тип списка',
            'options' => SaleFilters::TYPE_OF_FILTERS_ARRAY,
            'label' => 'Тип фильтра',
            'color' => 'primary'
        ]); ?>

    </div>
    <div class="col-sm-2">
        <?php echo \app\components\MdbSelect::widget([
            'request_type' => 'post',
            'name' => 'regions',
            'value' => $model->regions,
            'placeholder' => 'Районы',
            'options' => [10 => 'Любой'] + \app\models\SaleFilters::getRegions(),
            'label' => 'Районы',
            'color' => 'primary'
        ]); ?>

    </div>

    <div class="col-sm-2">

        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="col-sm-2">
        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>


</div>
