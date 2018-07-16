<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mdb;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $modele app\models\TagsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tags-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <? // echo  $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        <div class="col-sm-1">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'parent',
                'placeholder' => 'Parent',
                'options' => [10 => 'Любой'] + \app\models\Tags::PARENTS_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>

        </div>
        <div class="col-sm-2">

            <div class="md-form form-sm">
                <?= \app\components\MdbTextInput::widget([
                    'request_type' => 'get',
                    'name' => 'text_like',
                    'label' => 'название',
                ]); ?>
            </div>

        </div>
        <div class="col-sm-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'locality',
                'placeholder' => 'Локальность',
                'options' => ['any' => 'Любая',
                    'default' => 'Общая',
                    'Velikiy_Novgorod' => 'Великий Новгород'],
                'label' => '',
                'color' => 'primary'
            ]); ?>

        </div>
        <div class="col-sm-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'type',
                'placeholder' => 'Тип',
                'options' => [10 => 'Любой'] + \app\models\Tags::TYPES_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>

        </div>
        <div class="col-sm-2">
            <?php echo Mdb:: ActiveSelect($model, 'a_type', [0 => "ANY"] + Tags::A_TYPES + [999 => 'НЕТ']); ?>

        </div>
        <div class="col-sm-2">
            <?php echo Mdb:: ActiveCheckbox($model, 'searchable', ['label' => 'В поиске']); ?>
        </div>
        <div class="col-2">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'value' => $model->publish,
                'name' => 'publish',
                'placeholder' => 'Тип публичности',
                'options' => [10 => 'Любой'] + \app\models\Tags::PUBLIC_ARRAY,
                'label' => '',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-sm-1">
            <div class="form-group">
                <div class="row">
                    <div class="col-sm-12">
                        <?= Html::submitButton('Search', ['class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                    <div class="col-sm-12">
                        <?= Html::a('Create', ['create'], ['class' => 'btn btn-success btn-sm']) ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php // echo $form->field($model, 'color') ?>

    <?php // echo $form->field($model, 'komment') ?>



    <?php ActiveForm::end(); ?>

</div>
