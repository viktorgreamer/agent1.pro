<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 02.08.2017
 * Time: 12:47
 */

$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);

?>
    <div id='info'></div>
    <div id="<?= $map_id ?>" style="width:100%; height: 500px;"></div>
<?php

$script = <<<JS
// инициализация геообъектов
var module = $module;
var polygon_coords = $polygon;
var isEditablePolygon = $isEditablePolygon;
var map_id = '$map_id';
var Placemarks = $Placemarks;
    // Как только будет загружен API и готов DOM, выполняем инициализацию
    ymaps.ready(init);

    function init() {
        var myMap_$map_id = new ymaps.Map(map_id, {
                center: [module.coords_x, module.coords_y],
                zoom: module.zoom,
                controls: ['zoomControl', 'typeSelector', 'fullscreenControl']
            }),
            polygon = new ymaps.GeoObject({
                geometry: {
                    type: "Polygon",
                    coordinates: polygon_coords
                }

            });
        
        ButtonStartLayout = ymaps.templateLayoutFactory.createClass([
            '<button class=\"btn btn-success btn-rounded btn-sm waves-effect\" id=\"EditPolyline\"><i class=\"fa fa-pencil fa-fw\" aria-hidden="true"></i>Редактировать</button>'
        ].join(''));
        
        ButtonEditLayout = ymaps.templateLayoutFactory.createClass([
            '<button class=\"btn btn-success btn-rounded btn-sm waves-effect\" id=\"startDrawNew\"><i class=\"fa fa-pencil fa-fw\" aria-hidden="true"></i>Рисовать</button>'
        ].join(''));
        
               
        ButtonClearLayout = ymaps.templateLayoutFactory.createClass([
            '<button class=\"btn btn-danger btn-sm btn-rounded waves-effect\" id=\"regionselectclear\"><i class=\"fa fa-trash fa-fw\" aria-hidden=\"true\"></i> Удалить</button>'
        ].join('')),
        
        ButtonStopLayout = ymaps.templateLayoutFactory.createClass([
            '<button class=\"btn btn-primary btn-sm btn-rounded waves-effect\" id=\"stopEditPolyline\"><i class=\"fa fa-check fa-fw\" aria-hidden=\"true\"></i>Стоп</button>'
        ].join('')),
         ButtonRefreshLayout = ymaps.templateLayoutFactory.createClass([
            '<button class=\"btn btn-primary btn-sm btn-rounded waves-effect\" id=\"refreshPolyline\"><i class=\"fa fa-refresh fa-fw\" aria-hidden=\"true\"></i>Восставиноть</button>'
        ].join('')),

        buttonRefresh = new ymaps.control.Button({
            options: {layout: ButtonRefreshLayout}
        }); 
        buttonEdit = new ymaps.control.Button({
            options: {layout: ButtonEditLayout}
        }); 
        buttonClear = new ymaps.control.Button({
            options: {layout: ButtonClearLayout}
        });
       
        buttonStart = new ymaps.control.Button({
            options: {layout: ButtonStartLayout}
        });
         buttonStop = new ymaps.control.Button({
            options: {layout: ButtonStopLayout}
        });
         if (isEditablePolygon) {
             
          myMap_$map_id.controls.add(buttonClear, {
                right: 5,
                top: 5
            });
          myMap_$map_id.controls.add(buttonStop, {
                right: 5,
                top: 5
            });

            myMap_$map_id.controls.add(buttonStart, {
                right: 5,
                top: 5
            });  
            myMap_$map_id.controls.add(buttonRefresh, {
                right: 5,
                top: 5
            }); 
            myMap_$map_id.controls.add(buttonEdit, {
                right: 5,
                top: 5
            }); 
         }
         
           
            
           
        
        var myCollection = new ymaps.GeoObjectCollection();
       
        
        // инициализация Placemarks ;
       
        for (var i in Placemarks) {
           coords = Placemarks[i][0];
           contentproperties = Placemarks[i][1];
       // console.log(properties.balloonContentBody);
           myCollection.add(
               new ymaps.Placemark(coords, {
            balloonContentBody: contentproperties.balloonContentBody,
            iconContent:  contentproperties.iconContent,
            balloonPanelMaxMapArea: 0
            
           
        }));
        }
        
      

   
       

       // обработчики кнопок
         $(document).on('click','#stopEditPolyline',  function () {
                polygon.editor.stopEditing();
                printGeometry(polygon.geometry.getCoordinates());
                toastr.success('закончили рисовать');
                $('#regionselectclear').removeAttr('disabled');
                $('#startDrawNew').removeAttr('disabled');
            });

        $(document).on('click','#regionselectclear', function () {
      
            printGeometry();
            myCollection.remove(polygon);
            toastr.success('очистили полигон');
            $('#startDrawNew').removeAttr('disabled'); 
            $('#refreshPolyline').removeAttr('disabled'); 
           

        });
        $(document).on('click','#startDrawNew', function () {
            myCollection.remove(polygon);
            printGeometry();
            polygon = resetPolygon();
           myCollection.add(polygon);
            polygon.editor.startDrawing();
              toastr.success('начинаем рисовать');
            $('#EditPolyline').attr('disabled','disabled');
            $('#regionselectclear').attr('disabled','disabled');

        });
        $(document).on('click','#EditPolyline', function () {
             myCollection.add(polygon); 
            polygon.editor.startDrawing();
              toastr.success('редактируем');
              $('#startDrawNew').attr('disabled','disabled');
              $('#refreshPolyline').attr('disabled','disabled');
          


        });
        $(document).on('click','#get-zoom', function () {
           toastr.success('zoom = '+  myMap_$map_id.getZoom());


        });
        $(document).on('click','#refreshPolyline', function () {
              myCollection.add(polygon);
                printGeometry(polygon.geometry.getCoordinates());

        });
        
       function resetPolygon() {
        return new ymaps.GeoObject({
                geometry: {
                    type: "Polygon",
                    coordinates: []
                }

            });
        }

        


       myCollection.add(polygon);
      
       if (Placemarks.length == 1)  {
               myMap_$map_id.setCenter(Placemarks[0][0],17); 
               myMap_$map_id.geoObjects.add(myCollection);
           
           } else if (myMap_$map_id.getZoom() == undefined) {
             myMap_$map_id.setCenter([module.coords_x, module.coords_y],module.zoom); 
               myMap_$map_id.geoObjects.add(myCollection);
                 myMap_$map_id.setBounds(myCollection.getBounds());
                toastr.success("new zoom" + myMap_$map_id.zoom);
           } else {
               myMap_$map_id.geoObjects.add(myCollection);
            myMap_$map_id.setBounds(myCollection.getBounds());
           }
           
          // $("#info").html(myCollection.getBounds());
           // toastr.warning(myCollection.getBounds(),'ERROR', {"timeOut": "5000"});
           myMap_$map_id.options.set('balloonMinWidth',600);
       
      
      


       
       
        
       
    }
    
    
    function expandBounds(bounds,step) {
        step = 0.01;
     // console.log('was ' + bounds[0][0] + ' became '+ (bounds[0][0] +0.02));
      return [[bounds[0][0] - step, bounds[0][1] - step*3], [bounds[1][0] +  step, bounds[1][1] + step*3 ]];
    }


   // преобразразование геометрии в строковый формат
    function printGeometry(coords) {
        
        $('#poly').val(JSON.stringify(coords));
       toastr.success(JSON.stringify(coords));
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
?>
<?php $this->registerJs($script, yii\web\View::POS_READY);
