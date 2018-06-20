<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\AddressesSearch */
/* @var $form yii\widgets\ActiveForm */
if (!$model->status) $model->status = 10;
if (!$model->tagged) $model->tagged = 10;
$session = Yii::$app->session;
$module = $session->get('module');
// $salefilter = \app\models\SaleFilters::findOne($salefilter->id);


$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
?>


<div class="addresses-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>


    <? // echo $form->field($model, 'address') ?>

    <div class="row">
        <div class="col-3">
            <?= $form->field($model, 'locality')->textInput(['maxlength' => 100]) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'id')->textInput(['maxlength' => 100]) ?>
        </div>
        <div class="col-3">
            <?= $form->field($model, 'street')->textInput(['maxlength' => 100]) ?>
        </div>
        <div class="col-1">
            <?= $form->field($model, 'house')->textInput(['maxlength' => 5]) ?>
        </div>
        <div class="col-1">
            <?= $form->field($model, 'hull')->textInput(['maxlength' => 2]) ?>
        </div>
        <div class="col-1">

            <?php // echo $form->field($model, 'district') ?>

            <?php // echo $form->field($model, 'house_type') ?>

            <?php // echo $form->field($model, 'floorcount') ?>

            <?php // echo $form->field($model, 'address_string_variants') ?>

            <?php // echo $form->field($model, 'year') ?>

            <?php // echo $form->field($model, 'precision_yandex') ?>
            <?php
            // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'status',
                'value' => $model->status,
                'placeholder' => 'Статус',
                'options' => [
                    10 => 'Неважно',
                    0 => 'НеПроверенный',
                    1 => 'Проверенный',
                    2 => 'Нежилой объект'

                ],
                'label' => '',
                'color' => 'primary'
            ]);
            ?>
        </div>
        <div class="col-1">
            <?php
            // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'tagged',
                'value' => $model->tagged,
                'placeholder' => 'Статус',
                'options' => [
                    10 => 'Неважно',
                    1 => 'Нет тегов',
                    2 => 'Есть теги'

                ],
                'label' => '',
                'color' => 'primary'
            ]);
            ?>
        </div>

        <div class="col-md-3">
            <div class='text-center'>Этажность</div>
            <div class="row">
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <?php // выбор периода подачит обьявлений
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'floorcount_down',
                            'placeholder' => 'до',
                            'options' => \app\models\Sale::getFloors(),
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="md-form form-sm">
                        <?php // выбор периода подачит обьявлений
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'floorcount_up',
                            'placeholder' => 'до',
                            'options' => \app\models\Sale::getFloors(),
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-1">
            <div class="md-form form-sm">
                <?= \app\components\MdbTextInput::widget([
                    'request_type' => 'get',
                    'name' => 'year_down',
                    'label' => 'от',
                ]); ?>
            </div>
        </div>
        <div class="col-md-1">
            <div class="md-form form-sm">
                <?= \app\components\MdbTextInput::widget([
                    'request_type' => 'get',
                    'name' => 'year_up',
                    'label' => 'до',
                ]); ?>
            </div>
        </div>

        <button id="#search_by_Tags" type="button" class="btn btn-primary btn-sm"
                data-toggle="modal"
                data-target="#search_by_Tags"> search via tags
        </button>
        ";


        <div class="modal fade" id="search_by_Tags" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">

                <div class="modal-content">
                    <div class="modal-body">


                        <? echo $this->render('//tags/add-form-search-by-tags');

                        ?>




                    </div>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
            <button id="#regionset" type="button" class="btn btn-primary regionset"
                    data-toggle="modal"
                    data-target="#myModal">кАРТА
            </button>
        </div>
        <input type="text" name="polygon_text" id="poly" size="40" hidden
               value="<? echo $_GET['polygon_text']; ?>">

        <?php ActiveForm::end(); ?>

        <a id="#AddTagsAddress" type="button" href="#"
           data-toggle="modal"
           data-target="#TagsModal"> <i
                    class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
        </a>
        <a class=" btn btn-success tags-action-button-address-all" href="#"> add all tags </a>
        <div class="modal fade" id="TagsModal" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <? echo $this->render('//tags/quick-add-form-address', [
                            'id' => 1000000,
                        ]);
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="col-2">
                        <button class="btn btn-success btn-sm" id="regionselectstartdraw"> Рисование</button>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success  btn-sm" id="stopEditPolyline"> Завершить</button>
                    </div>
                    <div class="col-2">
                        <button class="btn btn-success btn-sm" id="regionselectclear"> Очистить</button>
                    </div>

                    <div class="col-2">
                        <button type="button" class="btn btn-success btn-sm" data-dismiss="modal"
                                aria-label="Close"
                                id="closeModal">
                            Закрыть
                        </button>
                    </div>
                </div>

            </div>


            <div id="map" style="width:100%; height: 500px;">

            </div>
        </div>
    </div>
</div>

<?php
if ($addresses) {
    $ids = [];
    foreach ($addresses as $address) {
array_push($ids,$address->id);
    }
    $session->set('addresses',$ids);
}
?>

<script type="text/javascript">
    // Как только будет загружен API и готов DOM, выполняем инициализацию
    ymaps.ready(init);

    function init() {
        var myMap_ = new ymaps.Map("map", {
            center: [<?= $module->coords_x ?> , <?= $module->coords_y ?>],
            zoom: <?= $module->zoom; ?>,
            controls: ['zoomControl', 'typeSelector', 'fullscreenControl']
        });
        polygon = new ymaps.GeoObject({
            geometry: {
                type: "Polygon",
                coordinates: <? if (!empty($model->polygon_text)) echo $model->polygon_text; else echo "[]"; ?>
            }

        });

        <?
        if ($addresses) {

            echo \app\models\Renders::YPlacemarks($addresses, 'address','');
        }
        ?>
        myMap.geoObjects.add(polygon);

        $('input').attr('disabled', false);

        // Обработка нажатия на любую кнопку.
        $('#stopEditPolyline').click(
            function () {
                // Отключаем кнопки, чтобы на карту нельзя было
                // добавить более одного редактируемого объекта (чтобы в них не запутаться).
                //  $('input').attr('disabled', true);

                polygon.editor.stopEditing();

                printGeometry(polygon.geometry.getCoordinates());

            });


        $('#regionselectclear').click(function () {
            printGeometry();
            myMap.geoObjects.remove(polygon);
            $('.regionset').html('<i class="fa fa-map-marker fa-inverse fa-2x" aria-hidden="true"></i>');

        });

        $('#regionselectstartdraw').click(function () {
            printGeometry();
            myMap.geoObjects.add(polygon);
            polygon.editor.startDrawing();


        });

        $('#closeModal').click(function () {
            var
                str_polygon = $('#poly').val();
            if (str_polygon.length == 0) {


                $('.regionset').html('<i class="fa fa-map-marker fa-inverse fa-2x" aria-hidden="true"></i>');
            } else {


                $('.regionset').html('<i class="fa fa-map-marker fa-2x" aria-hidden="true"></i>');


            }


        });


    }


    // Выводит массив координат геообъекта в <div id="geometry">
    function printGeometry(coords) {
        // $('#geometry').html('Координаты: ' + stringify(coords));
        $('#poly').val(JSON.stringify(coords));

        function stringify(coords) {
            var
                res = '';
            if ($.isArray(coords)) {
                res = '[';
                for (var i = 0, l = coords.length; i < l; i++) {
                    if (i > 0) {
                        res += ', ';
                    }
                    res += stringify(coords[i]);
                }
                res += ' ]';
            } else if (typeof coords == 'number') {
                res = coords.toPrecision(6);
            } else if (coords.toString) {
                res = coords.toString();
            }


            return res;
        }
    }


</script>