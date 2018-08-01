<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\SaleFiltersSearch2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sale-filters-search">

    <?php $form = ActiveForm::begin([
         'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'user_id',
                'placeholder' => 'Агент',
                'options' => User::getAvailableUsersAsArray(),
                'label' => 'Агент',
                'color' => 'primary'
            ]); ?>
        </div>

        <div class="col-sm-2">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'rooms_count',
                'placeholder' => 'Объект',
                'options' => \app\models\SaleFilters::TYPE_OF_OBJECTS_ARRAY,
                'label' => 'Объект',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'type',
                'placeholder' => 'Тип фильтра',
                'options' => \app\models\SaleFilters::TYPE_OF_FILTERS_ARRAY,
                'label' => 'Тип фильтра',
                'color' => 'primary'
            ]); ?>
        </div>

        <div class="col-sm-2">
            <? echo "<input type=\"text\" class=\"form-control\" name=\"name\" placeholder=\"поиск по имени\" value='" . Yii::$app->request->get('name') . "'>"; ?>
        </div>


        <div class="col-sm-2">

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>

            </div>
        </div>


        <?php ActiveForm::end(); ?>

    </div>
</div>
