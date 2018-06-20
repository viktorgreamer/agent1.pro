<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use app\models\SaleLists;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use app\components\SaleWidget;
use app\models\Sale;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form ActiveForm */
$session = Yii::$app->session;
$module = $session->get('module');

if ($salefilter->sort_by == 0) $salefilter->sort_by = '1';
if ($salefilter->period_ads == 0) $salefilter->period_ads = 14;
$_GET['rooms_count'] = explode(",", $salefilter->rooms_count);
$_GET['id_sources'] = explode(",", $salefilter->id_sources);


$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);

?>
<!-- Switch -->


<div class="sale-index">


    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'class' => 'form-control',
        'action' => 'search'
    ]) ?>


    <div class="row">
        <div class="col-md-2">
            <select name="rooms_count[]" class="selectpicker form-inline small-height" data-width="auto"
                    multiple="multiple"
                    size="2"
                    title="Комнат">
                <option value=1 <?php if (in_array(1, $_GET['rooms_count'])) echo "selected"; ?> >1к
                </option>
                <option value=2 <?php if (in_array(2, $_GET['rooms_count'])) echo "selected"; ?> >2к
                </option>
                <option value=3 <?php if (in_array(3, $_GET['rooms_count'])) echo "selected"; ?> >3к
                </option>
                <option value=4 <?php if (in_array(4, $_GET['rooms_count'])) echo "selected"; ?> >4к
                </option>
                <option value=5 <?php if (in_array(5, $_GET['rooms_count'])) echo "selected"; ?> >5+
                </option>
                <option value=30 <?php if (in_array(30, $_GET['rooms_count'])) echo "selected"; ?> >Комн.
                </option>
                <option value=20 <?php if (in_array(20, $_GET['rooms_count'])) echo "selected"; ?> >Студия
                </option>
            </select>

            <?= $form->field($salefilter, 'period_ads')->textInput(['size' => '2'])->label('')->dropDownList([
                '1' => '1д',
                '2' => '2д',
                '3' => '3д',
                '7' => '7д',
                '14' => '14д',
                '31' => '31д',
                '90' => '3м']); ?>
            <button id="#regionset" type="button" class="btn btn-primary regionset" data-toggle="modal"
                    data-target="#myModal">
                <? if ($salefilter->polygon_text != '') echo "<i class=\"fa fa-map-marker fa-inverse fa-2x\" aria-hidden=\"true\" style=\"color:green\"></i>";
                else echo "<i class=\"fa fa-map-marker fa-2x\" aria-hidden=\"true\"></i>"; ?> </button>
            <button id="#building" type="button" class="btn btn-primary btn-xs building" data-toggle="modal"
                    data-target="#buildingModal"> Больше параметров
            </button>
        </div>
        <div class="col-md-5">
            <div class="col-md-3">
                Цена
                <?= $form->field($salefilter, 'price_down')->textInput(['size' => 6, 'class' => 'form-inline small-height'])->label('') ?>
                <?= $form->field($salefilter, 'price_up')->textInput(['size' => 6, 'class' => 'form-inline small-height'])->label('') ?>
            </div>
            <div class="col-md-3">
                Площ.
                <?= $form->field($salefilter, 'grossarea_down')->textInput(['size' => 3, 'class' => 'form-inline small-height'])->label('От') ?>
                <?= $form->field($salefilter, 'grossarea_up')->textInput(['size' => 3, 'class' => 'form-inline small-height'])->label('До') ?>
            </div>
            <div class="col-md-3">
                Эт.
                <?= $form->field($salefilter, 'floor_down')->textInput(['size' => 2, 'class' => 'form-inline small-height'])->label('От') ?>
                <?= $form->field($salefilter, 'floor_up')->textInput(['size' => 2, 'class' => 'form-inline small-height'])->label('До') ?>
            </div>
            <div class="col-md-3">
                Этажн. <br>
                <?= $form->field($salefilter, 'floorcount_down')->textInput(['size' => 2, 'class' => 'form-inline small-height'])->label('От') ?>
                <?= $form->field($salefilter, 'floorcount_up')->textInput(['size' => 2, 'class' => 'form-inline small-height'])->label('До') ?>
                <?= $form->field($salefilter, 'not_last_floor')->checkbox()->label('') ?>

            </div>
        </div>
        <div class="col-md-3  small-height">
            <?= $form->field($salefilter, 'sort_by')->radioList([
                '1' => 'Цена',
                '2' => 'Время'])->label('') ?>


            <?= $form->field($salefilter, 'agents')->checkbox()->label('') ?>

            <?= $form->field($salefilter, 'housekeepers')->checkbox()->label('') ?>
            <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>

        </div>
        <div class="col-md-2">
            <select name="id_sources[]" class="selectpicker" data-width="auto" multiple="multiple"
                    size="3"
                    title="ресурс">
                <option value=1 <?php if (in_array(1, $_GET['id_sources'])) echo "selected"; ?> >
                    irr.ru
                </option>
                <option value=2 <?php if (in_array(2, $_GET['id_sources'])) echo "selected"; ?> >
                    yandex.ru
                </option>
                <option value=3 <?php if (in_array(3, $_GET['id_sources'])) echo "selected"; ?> >
                    avito.ru
                </option>
                <option value=5 <?php if (in_array(5, $_GET['id_sources'])) echo "selected"; ?> >
                    cian.ru
                </option>
                <option value=4 <?php if (in_array(4, $_GET['id_sources'])) echo "selected"; ?> >
                    youla.io
                <option value=6 <?php if (in_array(6, $_GET['id_sources'])) echo "selected"; ?> >
                    other
                </option>

            </select>
            <br>
            <input type="checkbox" name="savefilter">Сохранить фильтр<Br>
            <?php
            if ($session->getFlash('ExistedSaleFilter')) {
                ?>

                <div class="modal fade" id="ExistedSaleFilter" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                Фильтр с таким название существует, заменить ?
                                <button class="btn-primary salefilter-confirm-rewrite-button">Заменить</button>
                                <button type="button" class="btn-success" data-dismiss="modal" aria-label="Close"
                                        id="closeModal">Закрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?
                $this->registerJs(" $('#ExistedSaleFilter').modal('show');", yii\web\View::POS_END);
                $session->setFlash('ExistedSaleFilter', false);
            }


            ?>
            <?php
            if ($session->getFlash('ExistedSaleList')) {
                ?>

                <div class="modal fade" id="ExistedSaleList" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                               Список с таким название существует, заменить ?
                                <button class="btn-primary salelist-confirm-rewrite-button">Заменить</button>
                                <button type="button" class="btn-success" data-dismiss="modal" aria-label="Close"
                                        id="closeModal">Закрыть
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <?
                $this->registerJs(" $('#ExistedSaleList').modal('show');", yii\web\View::POS_END);
                $session->setFlash('ExistedSaleList', false);
            }


            ?>
            <? echo $form->field($salefilter, 'name')->textInput(['size' => 25])->label('') ?>




            <input type="checkbox" name="savelist">Сохранить список<Br>
            <? echo "<input type=\"text\" class=\"form-control\" name=\"salelist_name\" placeholder=\"имя списка\" value='".Yii::$app->request->get('salelist_name')."'>"; ?>




        </div>
    </div>


</div>


<!-- модальное окно параметров дома -->
<div class="modal fade" id="buildingModal" tabindex="-1" role="dialog" aria-labelledby="mybuildingModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" id="buildingModalContent">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Дополнительные параметры</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-3">
                        Год
                        <?= $form->field($salefilter, 'year_down')->textInput(['size' => 2])->label('От') ?>
                        <?= $form->field($salefilter, 'year_up')->textInput(['size' => 2])->label('До') ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($salefilter, 'is_super_filter')->checkbox()->label('Суперфильтр') ?>
                        <?= $form->field($salefilter, 'discount')->textInput(['size' => 2])->label('% дискаунта') ?>

                    </div>
                    <div class="col-md-3">
                        <?= $form->field($salefilter, 'phone')->textInput(['size' => 10])->label('телефон') ?>
                        <?= $form->field($salefilter, 'house_type')->label('Тип дома')->dropDownList([
                            '0' => 'Любой',
                            '1' => 'пан.',
                            '2' => 'кирп.',
                            '3' => 'монолит.',
                            '4' => 'блочн.',
                            '5' => 'дерев.']); ?>

                    </div>
                    <div class="col-md-3">


                        <?= $form->field($salefilter, 'text_like')->textInput(['size' => 20])->label('поиск по тексту') ?>
                        <? echo $form->field($salefilter, 'polygon_text', ['inputOptions' => ['id' => 'poly']])->hiddenInput()->label(''); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php ActiveForm::end(); ?>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <button class="btn-success" id="regionselectstartdraw"> Начать рисование</button>
                <button class="btn-success" id="stopEditPolyline"> Завершить редактирование</button>
                <button class="btn-success" id="regionselectclear"> Очистить</button>
                <button type="button" class="btn-success" data-dismiss="modal" aria-label="Close"
                        id="closeModal">
                    Закрыть
                </button>
                <div id="map">
                    <canvas id="canv"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo " all = " . count($sales);
echo " id_addresses = " . count(array_group_by($sales, 'id_address'));
?>


<script type="text/javascript">
    // Как только будет загружен API и готов DOM, выполняем инициализацию
    ymaps.ready(init);

    function init() {
        var myMap = new ymaps.Map("map", {
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
        /* if ($sales) {
              foreach ($sales as $sale) {
                  $SaleWidget = New SaleWidget();
                  $SaleWidget->sale = $sale;
                  $title = $SaleWidget->RenderTitle();
                  $days_ago = round(((time() - $sale->date_start) / 86400), 0) . " дн. назад";
                 echo \app\models\Renders::YPlacemark([
                      'id' => $sale->id,
                      'title' => $title,
                      'days_ago' => $days_ago,
                      'coords_x' => $sale->coords_x,
                      'coords_y' => $sale->coords_y,
                  ]);

              }
          }*/
        $sales = array_group_by($sales, 'id_address');
        echo \app\models\Renders::YPlacemarks($sales);
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
