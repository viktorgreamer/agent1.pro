<?php
/**
 * Created by PhpStorm.
 * User: phpNT - http://phpnt.com
 * Date: 28.04.2017
 * Time: 8:00
 */

namespace app\components\phpnt_yandex_map;

use yii\base\Widget;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class YandexMaps extends Widget
{
    public $myPlacemarks;
    public $mapOptions;
    public $additionalOptions = ['searchControlProvider' => 'yandex#search'];
    public $Polygon = [];

    public $disableScroll = true;

    public $windowWidth = '100%';
    public $windowHeight = '500px';

    public function init()
    {
        parent::init();
        $this->myPlacemarks = ArrayHelper::toArray($this->myPlacemarks);
        $this->mapOptions = Json::encode($this->mapOptions);
        $this->additionalOptions = Json::encode($this->additionalOptions);
        $this->disableScroll = $this->disableScroll ? 1 : 0;
        $this->registerClientScript();
    }

    public function run()
    {

        //dd($this->id);
        return $this->render(
            'view',
            [
                'widget' => $this
            ]);
    }

    public function registerClientScript()
    {
        $countPlaces = count($this->myPlacemarks);
        $items = [];
        $i = 0;
        foreach ($this->myPlacemarks as $one) {
            $items[$i]['latitude'] = $one['latitude'];
            $items[$i]['longitude'] = $one['longitude'];
            $items[$i]['options'] = $one['options'];
            $i++;
        }

        $myPlacemarks = json_encode($items);
        $myPolygon = json_encode($this->Polygon);
        $view = $this->getView();

        YandexMapsAsset::register($view);

        $js = <<< JS
        ymaps.ready(init);
            var myMap,
                myPlacemark;
        
            function init(){
                myMap = new ymaps.Map("$this->id", {$this->mapOptions}, {$this->additionalOptions});
                
                var disableScroll = $this->disableScroll;
                if ($this->disableScroll) {
                    myMap.behaviors.disable('scrollZoom');                    
                }

                var myPlacemarks = $myPlacemarks;        
        
                for (var i = 0; i < $countPlaces; i++) {
                    myPlacemark = new ymaps.Placemark([myPlacemarks[i]['latitude'], myPlacemarks[i]['longitude']],
                    myPlacemarks[i]['options'][0],
                    myPlacemarks[i]['options'][1],
                    myPlacemarks[i]['options'][2],
                    myPlacemarks[i]['options'][3],
                    myPlacemarks[i]['options'][4],
                    myPlacemarks[i]['options'][5]
                    );
                
                    myMap.geoObjects.add(myPlacemark);
                }
                // Создаем многоугольник, используя вспомогательный класс Polygon.
    // var polygon = new ymaps.Polygon([]);

    // Добавляем многоугольник на карту.
  //  myMap.geoObjects.add(polygon);
                }
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
            
JS;
        $view->registerJs($js,\yii\web\View::POS_END);
    }
}
