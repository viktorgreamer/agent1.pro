<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Addresses;
use app\models\Sale;
use app\models\Geocodetion;

/* @var $this yii\web\View */
/* @var $model app\models\Addresses */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="addresses-form">
        <?php $form = ActiveForm::begin(['enableAjaxValidation' => true]); ?>
        <div class="row">
            <div class="col-sm-4">
                <div class="row">
                    <div class="col-sm-7">
                        <?= \app\components\Mdb::ActiveTextInput($model, 'street', [
                            'id' => 'street',
                            'autocomplete' => ArrayHelper::getColumn(Addresses::getStreets(), 'street')
                        ]); ?>
                    </div>
                    <div class="col-sm-3">
                        <?= \app\components\Mdb::ActiveTextInput($model, 'house', ['id' => 'house']); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= \app\components\Mdb::ActiveTextInput($model, 'hull', ['id' => 'hull']); ?>
                    </div>
                    <div class="col-sm-2">
                        <?= \app\components\Mdb::ActiveSelect($model, 'balcon', Addresses::mapBalcon()); ?>
                    </div>
                </div>
                <div class="row">
                    <!--                        <div id="fulladdress"></div>-->
                    <?= \app\components\Mdb::ActiveTextInput($model, 'address', ['label' => false, 'disabled' => 'disabled', 'id' => 'address']); ?>
                </div>
                <div class="row">
                    <?= \app\components\Mdb::ActiveTextInput($model, 'AdministrativeAreaName', ['id' => 'AdministrativeAreaName','autocomplete' => ArrayHelper::getColumn(Addresses::getRegions(), 'AdministrativeAreaName')]); ?>
                </div>
            </div>
            <div class="col-sm-2">
                <?= \app\components\Mdb::ActiveTextInput($model, 'year'); ?>
                <?= \app\components\Mdb::ActiveSelect($model, 'status', Addresses::STATUSES); ?>
                <? echo \app\components\Mdb::ActiveTextInput($model, 'locality', ['autocomplete' => ArrayHelper::getColumn(Addresses::getLocalities(), 'locality')]); ?>
            </div>
            <div class="col-sm-3">
                <?= \app\components\Mdb::ActiveTextInput($model, 'district', ['autocomplete' => ArrayHelper::getColumn(Addresses::getDistricts(), 'district')]); ?>
                <?= \app\components\Mdb::ActiveTextInput($model, 'coords_x', ['id' => 'coords_x']); ?>
                <?= \app\components\Mdb::ActiveTextInput($model, 'coords_y', ['id' => 'coords_x']); ?>
                <?= \app\components\Mdb::ActiveTextInput($model, 'tags_id'); ?>
            </div>
            <div class="col-sm-3">
                <?= \app\components\Mdb::ActiveSelect($model, 'house_type', Sale::HOUSE_TYPES); ?>
                <?= \app\components\Mdb::ActiveSelect($model, 'floorcount', Sale::getFloors()); ?>
                <?= \app\components\Mdb::ActiveTextInput($model, 'precision_yandex', ['autocomplete' => Geocodetion::PRECISION_YANDEX]); ?>
            </div>
        </div>


        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>

        </div>
        <?php ActiveForm::end(); ?>
    </div>

<?php
$script = <<< JS
 //  generateAddress();
$("#street").on('change', function (e) {
    generateAddress();
});
$("#hull").on('change', function (e) {
    generateAddress();
});
$("#house").on('change', function (e) {
    generateAddress();
});

function  generateAddress() {
    var street = $("#street").val();
    var house = $("#house").val();
    var hull = $("#hull").val();
    var address = '';
    if (street) {
       //  toastr.success(street);
        address = street;
    }
    if (house) {
        //  toastr.success(house);
        address = address + ',' +house;
    }
    if (hull != '-')        {
     // toastr.success(hull);
        address = address + ' к' +hull;
    }
  $("#fulladdress").html(address);
  $("#address").val(address);
 // toastr.success(address);
  
}
JS;

//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);


