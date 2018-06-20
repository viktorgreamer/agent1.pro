<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SaleLists;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $model app\models\SaleLists */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-lists-form">
    <?php $form = ActiveForm::begin(); ?>



    <? // echo $form->field($model, 'user_id')->textInput() ?>

    <? echo $form->field($model, 'name')->textInput() ?>
    <? echo $form->field($model, 'komment')->textInput() ?>
    <div class="row">
        <div class="col-lg-3">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'post',
                'name' => 'type',
                'value' => $model->type,
                'placeholder' => 'Тип списка',
                'options' => \app\models\SaleFilters::TYPE_OF_FILTERS_ARRAY,
                'label' => 'Тип списка',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-lg-3">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'post',
                'name' => 'regions',
                'value' => $model->regions,
                'placeholder' => 'Районы',
                'options' => \app\models\SaleFilters::getRegions(),
                'label' => 'Районы',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-lg-6">

                <?php echo \app\components\MdbSelect::widget([
                    'request_type' => 'post',
                    'name' => 'parent_salefilter',
                    'value' => $model->parent_salefilter,
                    'multiple' => true,
                    'placeholder' => 'Родители Фильтры',
                    'options' => $model->getParentsSaleFilters('all'),
                    'label' => 'Родители Фильтры',
                    'color' => 'primary'
                ]); ?>
            </div>

    </div>




    <?php
    $salelists = SaleLists::find()->where(['in', 'id', $model->getRelevantedByTags(10)])->andWhere(['<>', 'id', $model->id])->all();
    foreach ($salelists as $salelist1) {
        $salelist1 = \app\models\SaleLists::findOne($salelist1->id);
        ?>
        <div class="row">
            <h5><a href="#" class="badge cyan trigger-similar-lists" data-salelist_id="<?php echo $model->id; ?>"
                   data-similar_id="<?php echo $salelist1->id; ?>"><?php echo $salelist1->name; ?> </a></h5>
        </div>


        <?
    }


    if ($model->similar_lists) {
        $existed_lists = explode(",", $model->similar_lists);
        foreach ($existed_lists as $salelist2) {
            $salelist2 = \app\models\SaleLists::findOne($salelist2);
            ?>
            <div class="row">
                <h5><a href="#" class="badge success-color trigger-similar-lists"
                       data-salelist_id="<?php echo $model->id; ?>"
                       data-similar_id="<?php echo $salelist2->id; ?>"><?php echo $salelist2->name; ?> </a></h5>
            </div>
        <? }
    } ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>
