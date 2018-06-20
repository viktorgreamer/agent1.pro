<?php

use app\models\SaleFilters;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use app\components\Mdb;
use yii\helpers\Url;
$session = Yii::$app->session;

?>

<form class='row' action="search-by-filter" method="get" id="w0">
    <div class="col-sm-2">
        <?php echo Mdb::Select('type_of_show', 'Тип показа', SaleFilters::TYPE_OF_SHOW_ARRAY); ?>
    </div>
    <div class="col-md-1">
        <div class="md-form form-sm">
            <?php echo Mdb::Select('view', 'Тип показа', SaleFilters::TYPE_OF_VIEWS); ?>
        </div>
    </div>
    <div class="col-md-2">
        <?php echo Mdb::ActiveSelect($salefilter, 'uniqueness', SaleFilters::TYPE_OF_UNIQUING, ['label' => 'Уникальность']); ?>
    <?php echo Html::hiddenInput('id', $salefilter->id); ?>
    </div>
    <div class="col-md-2">
        <?php echo Mdb::ActiveSelect($salefilter, 'sort_by', SaleFilters::TYPE_OF_SORTING_ARRAY,  ['label' => 'Сортировка']); ?>
    </div>
    <div class="col-md-2">
        <?php echo Mdb::ActiveSelect($salefilter, 'sale_disactive', [10 => 'Любой', 0 => 'Активный', 1 => 'Продано', 2 => 'Пропало', '4' => "РЕАЛЬНЫЕ"], ['label' => 'Статус']); ?>
    </div>
    <div class="col-sm-1">
        <?php echo Html::a('update', Url::to(['sale/index2', 'id' => $salefilter->id]), ['target' => '_blank']); ?>
        <div class="form-group">
            <input type="checkbox" id="checkbox1" name="export">
            <label for="checkbox1">export</label>
        </div>
        <? if ($_GET['export']) { ?>
            <button type="button" class="btn btn-success btn-sm btn-rounded" data-toggle="modal"
                    data-target="#exportModal">
                EXPORT
            </button>
        <? } ?>
    </div>
    <div class="col-sm-1">
        <div class="form-group">
            <?= Html::submitButton(Mdb::Fa('refresh'), ['class' => 'btn btn-rounded btn-sm btn-success']) ?>

        </div>

    </div>


</form>