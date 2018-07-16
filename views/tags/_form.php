<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Tags */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tags-form form-inline">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-2">
            <div class="md-form form-sm">
                <?= app\components\MdbTextInput::widget([
                    'request_type' => 'get',
                    'value' => $model->name,
                    'name' => 'name',
                    'label' => 'имя',
                ]); ?>
            </div>
        </div>
        <div class="col-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'parent',
                'value' => $model->parent,
                'placeholder' => 'Parent',
                'options' => \app\models\Tags::PARENTS_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'locality',
                'value' => $model->locality,
                'placeholder' => 'Локальность',
                'options' => ['default' => 'Общая',
                    'Velikiy_Novgorod' => 'Великий Новгород'],
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-1">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'value' => $model->type,
                'name' => 'type',
                'placeholder' => 'Тип',
                'options' => \app\models\Tags::TYPES_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-1">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'value' => $model->publish,
                'name' => 'publish',
                'placeholder' => 'Тип публичности',
                'options' => \app\models\Tags::PUBLIC_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'color',
                'value' => $model->color,
                'placeholder' => 'Важность',
                'options' => ['default' => 'обычный',
                    'danger' => 'Отрицательное',
                    'success' => 'Положительное',
                    'black' => 'Существенный минус'],
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-2">
            <div class="md-form form-sm">
                <?= app\components\MdbTextInput::widget([
                    'request_type' => 'get',
                    'value' => $model->komment,
                    'name' => 'komment',
                    'label' => 'комментарий',
                ]); ?>
            </div>
        </div>
    </div>



    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
