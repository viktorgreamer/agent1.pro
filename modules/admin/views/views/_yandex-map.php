<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form ActiveForm */
$session = Yii::$app->session;
$module = $session->get('module');


$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
?>
    <div id="map_<?= $id ?>" style="width:100%; height: 500px;">
    </div>

<script type="text/javascript">
    // Как только будет загружен API и готов DOM, выполняем инициализацию
    ymaps.ready(init);

    function init() {
        var myMap_<?= $id ?> = new ymaps.Map("map_<?= $id ?>", {
            center: [<?= $module->coords_x ?> , <?= $module->coords_y ?>],
            zoom: <?= $module->zoom; ?>,
            controls: ['zoomControl', 'typeSelector', 'fullscreenControl']
        });
        polygon = new ymaps.GeoObject({
            geometry: {
                type: "Polygon",
                coordinates: <? if (!empty($salefilter->polygon_text)) echo $salefilter->polygon_text; else echo "[]"; ?>
            }

        });

        <?
        if ($sales) {
            $sales = array_group_by($sales, 'id_address');
            echo \app\models\Renders::YPlacemarks($sales,'main',$id);
        }
        ?>
        myMap_<?= $id ?>.geoObjects.add(polygon);

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
            myMap_<?= $id ?>.geoObjects.remove(polygon);
            $('.regionset').html('<i class="fa fa-map-marker fa-inverse fa-2x" aria-hidden="true"></i>');

        });

        $('#regionselectstartdraw').click(function () {
            printGeometry();
            myMap_<?= $id ?>.geoObjects.add(polygon);
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
