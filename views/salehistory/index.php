<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use app\models\SaleLists;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form ActiveForm */
?>
<div class="sale-index">

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'action' => 'search'
    ]) ?>


    <div class="row">
        <div class="col-xs-1">Тип
            <br>
            <?= $form->field($salefilter, 'rooms_count')->dropdownList([
                0 => 'Все',
                1 => '1к',
                2 => '2к',
                3 => '3к',
                4 => '4к',
                5 => '5к',
                30 => 'ком'],
                ['options' => ['class' => 'selectpicker']])->label('') ?>
            <br>

            <?= $form->field($salefilter, 'period_ads')->label('')->dropDownList([
                '1' => '1д',
                '2' => '2д',
                '3' => '3д',
                '7' => '7д',
                '14' => '14д',
                '31' => '31д',
                '90' => '3м']) ?>

        </div>

        <div class="col-xs-2"> Цена <br>
           От <?= $form->field($salefilter, 'price_down')->textInput(['size' => 7])->label('') ?> <br>
            До<?= $form->field($salefilter, 'price_up')->textInput(['size' => 7])->label('') ?>
        </div>
        <div class="col-xs-2"> Площ. <br>
       От <?= $form->field($salefilter, 'grossarea_down')->textInput(['size' => 3])->label('') ?><br>
        До <?= $form->field($salefilter, 'grossarea_up')->textInput(['size' => 3])->label('') ?>
       </div>

        <div class="col-xs-2"> Эт. <br>
       От <?= $form->field($salefilter, 'floor_down')->textInput(['size' => 2])->label('') ?><br>
       До <?= $form->field($salefilter, 'floor_up')->textInput(['size' => 2])->label('') ?> <br>
            <?= $form->field($salefilter, 'not_last_floor')->checkbox()->label('') ?>
            </div>
            <div class="col-xs-2"> Этажн. <br>
            От<?= $form->field($salefilter, 'floorcount_down')->textInput(['size' => 2])->label('') ?><br>
        До<?= $form->field($salefilter, 'floorcount_up')->textInput(['size' => 2])->label('') ?>
                </div>
        <div class="col-xs-2">
            Цена/Время<br>
        <?= $form->field($salefilter, 'sort_by')->radioList([
            '1'=>'',
            '2'=>''])->label('') ?>
            </div>
        <div class="col-xs-2">
            Хозяин/Агент/Все <br>
            <?= $form->field($salefilter, 'status_blacklist2')->radioList([
                '1' => '',
                '2' => '',
                '3' => '',
            ])->label('') ?>
        </div>
    </div>

    <div class="col-xs-2"> Сохранить фильтр под названием <br>
         <?= $form->field($salefilter, 'name')->textInput(['size' => 20])->label('') ?> <br>

    </div>

    <?php $salefilter->create_modal_map(54,34) ?>
    <input type="hidden" id="poly" name="polygon" value="<?php echo $_POST['polygon']; ?>"><br>
    <button id="#regionset" type="button" class="btn btn-primary btn-xs regionset" data-toggle="modal"
            data-target="#myModal">
        Область на карте
    </button>
    <div class="col-xs-2"> Сохранить список под названием <br>
        <?= $form->field($salelist, 'name')->textInput(['size' => 20])->label('') ?> <br>

    </div>


    <div class="form-group">
        <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить фильтр', ['class' => 'btn btn-primary', 'formaction' => '/sale/save-current-filter'])
        ?>
    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить список', ['class' => 'btn btn-primary', 'formaction' => '/sale/save-current-list'])
        ?>
    </div>
    <?php  if ($update_button) { ?>
        <div class="form-group">
            Фильтр с данным имене существует, заменить?
            <?= Html::submitButton('Да', ['class' => 'btn btn-danger', 'formaction' => '/sale/update-current-filter'])
            ?>
        </div>

        <?php
    }

   if ($update_list_button) { ?>
        <div class="form-group">
            Список с данным имене существует, заменить?
            <?= Html::submitButton('Да', ['class' => 'btn btn-danger', 'formaction' => '/sale/update-current-list'])
            ?>
        </div>

        <?php
    }

    ActiveForm::end(); ?>




</div>

<div class="container">
    <table class='table table-striped'>
        <thead>
        <tr>
            <th>картинка</th>
            <th>Ком</th>
            <th>Адрес</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Пл.</th>
            <th>Телефон</th>
            <th>Url</th>
        </tr>
        </thead>
        <tbody>


        <?php foreach ($data as $tr): ?>
            <tr>

                <td>
                    <?php
                    if (!empty($tr['images'])) {
                        $list_of_images = explode("X", $tr['images']);
                        $id = $tr['id'];
                        echo "
								
								
								<a href=\"view?id=" . $id . "\"><img src=" . $list_of_images[1] . " width=\"100\" height=\"100\" > </a>";

                    }

                    ?>

                </td>

                <td><?php if ($tr['rooms_count'] == 30) echo "Комн."; else echo $tr['rooms_count'] . "к"; ?></td>

                <td><?php echo $tr['address']; ?>
                    <br> <?php echo $tr['floor'] . "/" . $tr['floorcount'] . "эт."; ?>
                    <br> <?php echo round(((time() - $tr['date_start']) / 86400), 0) . " дн. назад"; ?>

                </td>
                <td><?php echo $tr['description']; ?></td>
                <td><?php echo $tr['price']; ?></td>
                <td><?php echo $tr['grossarea']; ?></td>
                <td>
                    <?php if ($tr['status_blacklist2'] == 1) { ?>
                        <button class='btn btn-info'><?php echo $tr['phone1']; ?></button>
                    <?php } elseif ($tr['status_blacklist2'] == 2) { ?>
                        <button class='btn btn-danger'><?php echo $tr['phone1']; ?></button>
                    <?php } elseif ($tr['status_blacklist2'] == 3) { ?>
                        <button class='btn btn-success'><?php echo $tr['phone1']; ?></button>
                    <?php }  else { ?>
                    <button class='btn btn-default'><?php echo $tr['phone1']; ?></button>

                  <?  }?>
                </td>
                <td><a href="<?php echo $tr['url']; ?>" target="_blank">Link</a></td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>


<?php
// display pagination
echo LinkPager::widget([
    'pagination' => $pages,
]);
?>

<script>


    var Map = (function() {

        ymaps.ready(init);
        var myMap;

        function init() {
            myMap = new ymaps.Map("map", {
                center: [window.lat, window.long],
                zoom: 11
            }, {
                balloonMaxWidth: 200,
                searchControlProvider: 'yandex#search'
            });

            //addPolygon();
        }


        function convert(coords) {
            var projection = myMap.options.get('projection');

            return coords.map(function(el) {
                var c = projection.fromGlobalPixels(myMap.converter.pageToGlobal([el.x, el.y]), myMap.getZoom());
                return c;
            });
        }

        function addPolygon(coord) {
            areas = coord;
            var myGeoObject = new ymaps.GeoObject({
                geometry: {
                    type: "Polygon",
                    coordinates: [coord],
                },
            }, {
                fillColor: '#00FF00',
                strokeColor: '#0000FF',
                opacity: 0.5,
                strokeWidth: 3
            });

            myMap.geoObjects.add(myGeoObject);
        }

        function removeGeo() {
            myMap.geoObjects.removeAll();
        }

        return {
            addPolygon: addPolygon,
            removeGeo: removeGeo,
            convert: convert
        };
    })();

    //----------------------

    var canv = document.getElementById('canv');
    var ctx = canv.getContext('2d');
    areas = [];
    line = [];
    startX = 0;
    startY = 0;
    ofsleft = 0;
    ofstop = 0;


    var map = document.getElementById('map');
    canv.width = map.offsetWidth;
    canv.height = map.offsetWidth;


    var startX = 0,
        startY = 0;

    function mouseDown(e) {
        ctx.clearRect(0, 0, canv.width, canv.height);

        Map.removeGeo();

        startX = e.pageX - e.target.offsetLeft;
        startY = e.pageY - e.target.offsetTop;

        canv.addEventListener('mouseup', mouseUp);
        canv.addEventListener('mousemove', mouseMove);


        line = [];
        line.push({
            x: startX,
            y: startY
        });
    }

    /*
     function mouseMove(e) {
     var x = e.pageX - e.target.offsetLeft,
     y = e.pageY - e.target.offsetTop;

     ctx.beginPath();
     ctx.moveTo(startX, startY);
     ctx.lineTo(x, y);
     ctx.stroke();

     startX = x;
     startY = y;
     line.push({
     x: x,
     y: y
     });
     }
     */

    function mouseMove(e) {
        var x = e.pageX; // - e.target.offsetLeft,
        y = e.pageY; //- e.target.offsetTop;

        ctx = canv.getContext('2d'),
            ctx.beginPath();
        ctx.moveTo(startX-ofsleft-$(document).scrollLeft(), startY-ofstop-$(document).scrollTop());
        ctx.lineTo(x-ofsleft-$(document).scrollLeft(), y-ofstop-$(document).scrollTop());
        ctx.stroke();

        startX = x;
        startY = y;
        line.push({
            x: x,
            y: y
        });
    }



    function mouseUp() {
        canv.removeEventListener('mouseup', mouseUp);
        canv.removeEventListener('mousemove', mouseMove);

        aproximate();
    }

    function aproximate() {
        ctx.clearRect(0, 0, canv.width, canv.height);
        var res = simplify(line, 5);
        res = Map.convert(res);
        Map.addPolygon(res);
    }

    canv.addEventListener('mousedown', mouseDown);

    $('#regionset').click(function(e) {

        /*
         e.preventDefault();
         $('#shadow #view').hide();
         $('#shadow #map').hide();
         $('#shadow #map2').show();
         $('#shadow').show();
         $('#canv').hide();
         */

        var $div = $('#map2 > .popup-main');
        ofsleft = $div.prop('offsetLeft');
        ofstop = $div.prop('offsetTop');
    });

    $('#regionselect').click(function() {
        $('#canv').show();
        var $div = $('#map');
        ofsleft = $div.offset().left;
        ofstop = $div.offset().top;
        var c = document.getElementById('canv');
        var map = document.getElementById('map');
        c.width = map.offsetWidth;
        c.height = map.offsetHeight;
    });


    $('#regionselectoff').click(function() {
        $('#canv').hide();
    });

    $('#regionselectclear').click(function() {
        Map.removeGeo();
        areas = [];
    });

    $('#regionselectok').click(function() {
        $('#poly').empty();
        $('#poly').val(JSON.stringify(areas));
        if (areas.length > 0) {
            $('.regionset').text('Область на карте (задано)');
        } else {
            $('.regionset').text('Область на карте');
        }
        $('#myModal').modal('hide');
    });

    $('#fotoModal').on('shown.bs.modal', function () {
        $('#myInput').focus()
    })


    $('.carousel').carousel()




</script>
<!-- sale-index -->
