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
    <div class="col-lg-3 col align-self-center">
        <div class="row">
            <div class="switch  col-lg-6 primary-switch col">
                <label class="m-0 p-0 text-primary">
                    <?= Html::img('/web/icons/list.png', ['height' => '25px', 'title' => 'Список']); ?>
                    <?php echo Html::checkbox('view', $_GET['view']); ?>
                    <span class="lever m-1"></span>
                    <?= Html::img('/web/icons/map.png', ['height' => '25px', 'title' => 'Карта']); ?>
                </label>
            </div>
            <div class="switch col-lg-6 primary-switch col">
                <label class="m-0 p-0 text-primary">
                    <?= Html::img('/web/icons/copy.png', ['height' => '25px', 'title' => 'Все']); ?>
                    <?php echo Html::checkbox('uniqueness', $salefilter->uniqueness); ?>

                    <span class="lever m-1"></span>
                    <?= Html::img('/web/icons/unique.png', ['height' => '25px', 'title' => 'Уникальные']); ?>
                </label>
            </div>
        </div>
    </div>
    <?php echo Html::hiddenInput('id', $salefilter->id); ?>
    <div class="col-md-4 col-lg-1 col-sm-6 col-6">
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
            <?= Html::submitButton(ICON_SEARCH,['class' => CLASS_BUTTON]) ?>
            <?php echo Html::a(ICON_EDIT2, Url::to(['sale/index2', 'id' => $salefilter->id]), ['class' => CLASS_BUTTON, 'target' => '_blank']); ?>
        </div>

    </div>


</form>