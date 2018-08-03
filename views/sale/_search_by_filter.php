<?php

use app\models\SaleFilters;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\Mdb;
use yii\helpers\Url;

$session = Yii::$app->session;

?>

<form class='row' action="search-by-filter" method="get" id="w0">
    <div class="col-md-4 col-lg-3 col-sm-6 col-6 ">
        <?php echo Mdb::Select('type_of_show', 'Тип показа', SaleFilters::TYPE_OF_SHOW_ARRAY); ?>
    </div>
    <div class="row">
        <div class="col-lg-1 m-0 p-0"></div>
        <div class="col-md-4 col-sm-2 col-2 col-lg-2  m-0 p-0">
            <?= Html::img('/web/icons/s2.png', ['height' => '32px', 'title' => 'Уникальные']); ?>

        </div>
        <div class="col-md-4 col-sm-5 col-5 col-lg-3 m-0 p-0">
            <div class="md-form form-sm p-0 m-0">
                <?= Mdb::ActiveTextInput($salefilter, 'grossarea_down', ['label' => 'от']); ?>
            </div>
        </div>
        <div class="col-lg-1 m-0 p-0"></div>

        <div class="col-md-4 col-sm-5 col-5 col-lg-3 m-0 p-0">
            <div class="md-form form-sm p-0 m-0">
                <?= Mdb::ActiveTextInput($salefilter, 'grossarea_up', ['label' => 'до']); ?>
            </div>
        </div>
    </div>
    <?php echo Html::hiddenInput('id', $salefilter->id); ?>
    <div class="col-md-4 col-lg-2 col-sm-6 col-6">
        <?php echo Mdb::ActiveSelect($salefilter, 'sort_by', SaleFilters::TYPE_OF_SORTING_ARRAY, ['label' => 'Сортировка']); ?>
    </div>
    <div class="col-md-4 col-lg-2 col-sm-6 col-6">
        <?php echo Mdb::ActiveSelect($salefilter, 'sale_disactive', [10 => 'Любой', 0 => 'Активный', 1 => 'Продано', 2 => 'Пропало', '4' => "РЕАЛЬНЫЕ"], ['label' => 'Статус']); ?>
    </div>
    <!-- <div class="form-group">
            <input type="checkbox" id="checkbox1" name="export">
            <label for="checkbox1">export</label>
        </div>
        <? /* if ($_GET['export']) { */ ?>
            <button type="button" class="btn btn-success btn-sm btn-rounded" data-toggle="modal"
                    data-target="#exportModal">
                EXPORT
            </button>
        --><? /* } */ ?>
    <div class="col-md-4 col-lg-2 col-sm-6 col-6 no-margin-botton">
        <div class="form-group">
            <?= Html::submitButton(Mdb::Fa('refresh'), ['class' => 'btn btn-rounded btn-sm btn-success']) ?>
            <?php echo Html::a(ICON_EDIT2, Url::to(['sale/index2', 'id' => $salefilter->id]), ['class' => CLASS_BUTTON, 'target' => '_blank']); ?>


        </div>

    </div>


</form>