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
    <form method="get" class="row" action="search-by">


        <div class="col-sm-2">
            <?php echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'type',
                'id' => 'type_of_filter_id',
                'multiple' => 'true',
                'placeholder' => 'Тип списка',
                'options' => SaleFilters::TYPE_OF_FILTERS_ARRAY,
                'label' => 'Тип фильтра',
                'color' => 'primary'
            ]); ?>
        </div>
        <div id="names" class="col-sm-4">
            <?php

            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'id',
                'placeholder' => 'Название списка',
                'options' => \app\models\SaleLists::getMyListsAsArray($_GET['type']),
                'label' => 'Название списка',
                'color' => 'primary'
            ]); ?>
        </div>
        <div class="col-md-1">
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'unique',
                'value' => $_GET['unique'],
                'placeholder' => 'уникальные',
                'options' => [0 => 'нет', 1 => 'Да', 2 => 'extra'],
                'label' => 'Уникальные',
                'color' => 'primary'
            ]);
            ?>
        </div>
        <div class="col-md-1">
            <?php
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'sort_by',
                'placeholder' => 'до',
                'options' => [1 => 'время', 2 => 'цена', 3 => 'адресам'],
                'label' => 'сортировка',
                'color' => 'primary'
            ]);
            ?>
        </div>
        <div class="col-md-1">
            <?php
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'moderated_status',
                'placeholder' => 'Статус модерации',
                'options' => [1 => 'Новые', 2 => 'Ok', 3 => 'Ban'],
                'label' => 'Статус Модерации',
                'color' => 'primary'
            ]);
            ?>
        </div>

        <div class="col-sm-2">
            <div class="form-group">
                <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
            </div>

        </div>

    </form>
</div>
<?php
