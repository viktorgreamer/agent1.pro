<?php


use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mdb;
use app\models\Tags;

/* @var $this yii\web\View */
/* @var $model app\models\Tags */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="tags-form form-inline">
        <?php $form = ActiveForm::begin(); ?>
        <form method="post">

            <div class="row">
                <div class="col-2">
                    <div class="md-form form-sm">
                        <?= Mdb::ActiveTextInput($model, 'name'); ?>
                    </div>
                </div>
                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'global_parent', Tags::PARENTS_ARRAY); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'locality', ['default' => 'Общая',
                        'Velikiy_Novgorod' => 'Великий Новгород']); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'type', Tags::TYPES_ARRAY, ['id' => 'type']); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'id_parent', [0 => "Нет"] + Tags::mapParents(), ['id' => 'a_type']); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'publish', Tags::PUBLIC_ARRAY); ?>
                </div>

                <div class="col-2">
                    <?php echo Mdb::ActiveSelect($model, 'color', Tags::COLORS); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb:: ActiveSelect($model, 'a_type', [0 => "NO"] + Tags::A_TYPES); ?>
                </div>
                <div class="col-2">
                    <?php echo Mdb:: ActiveCheckbox($model, 'searchable', ['label' => 'учавствует в поиске']); ?>
                </div>
                <div class="col-2">
                    <div class="md-form form-sm">
                        <?= Mdb::ActiveTextInput($model, 'komment'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="md-form">
                <textarea type="text" id="form7"
                          name="patterns"
                          class="md-textarea"><? if ($model->patterns) echo $model->patterns; ?></textarea>
                        <label for="form7">autoload patterns</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="md-form">
                <textarea type="text" id="form11"
                          name="minus_patterns"
                          class="md-textarea"><? if ($model->minus_patterns) echo $model->minus_patterns; ?></textarea>
                        <label for="form11">autoload -patterns</label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm']) ?>
            </div>

            <?php ActiveForm::end(); ?>

    </div>
<?php

