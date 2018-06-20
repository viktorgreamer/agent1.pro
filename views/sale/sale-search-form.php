<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SaleFilters;
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
// $salefilter = \app\models\SaleFilters::findOne($salefilter->id);

if ($salefilter->sort_by == 0) $salefilter->sort_by = 2;
if ($salefilter->period_ads == 0) $salefilter->period_ads = 14;
if (!isset($salefilter->sale_disactive)) $salefilter->sale_disactive = 10;
$_GET['rooms_count'] = explode(",", $salefilter->rooms_count);
$_GET['id_sources'] = explode(",", $salefilter->id_sources);


$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
?>
<!-- Switch -->


<div class="sale-index">


    <form action="search" method="get" id="w0">


        <div class="row">

            <div class="col-md-3">
                <div class="row">
                    <div class="col-md-6">

                        <?php // выбор кол-ва комнат
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'rooms_count',
                            'value' => $salefilter->rooms_count,
                            'multiple' => 'true',
                            'placeholder' => 'объект',
                            'options' => \app\models\Sale::ROOMS_COUNT_ARRAY,
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                        <?php // выбор периода подачит обьявлений
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'period_ads',
                            'placeholder' => 'период',
                            'options' => \app\models\SaleFilters::DEFAULT_PERIODS_ARRAY,
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <?php
                            echo \app\components\MdbSelect::widget([
                                'request_type' => 'get',
                                'name' => 'sort_by',
                                'placeholder' => 'до',
                                'options' => [1 => 'время', 2 => 'цена', 3 => 'адресам'],
                                'label' => 'сортировка',
                                'color' => 'primary'
                            ]);
                            ?>
                        </div>
                        <div class="row">
                            <button id="#regionset" type="button" class="btn btn-primary regionset"
                                    data-toggle="modal"
                                    data-target="#myModal">кАРТА
                                <? // if ($salefilter->polygon_text != '') echo "<i class=\"fa fa-map-marker fa-inverse fa-2x\" aria-hidden=\"true\" style=\"color:green\"></i>";
                                //else echo "<i class=\"fa fa-map-marker fa-2x\" aria-hidden=\"true\"></i>"; ?> </button>
                        </div>

                    </div>
                </div>


            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class='text-center'> Цена</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?= \app\components\MdbTextInput::widget([
                                        'request_type' => 'get',
                                        'name' => 'price_down',
                                        'label' => 'от',
                                    ]); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?= \app\components\MdbTextInput::widget([
                                        'request_type' => 'get',
                                        'name' => 'price_up',
                                        'label' => 'до',
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class='text-center'>Площадь</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?= \app\components\MdbTextInput::widget([
                                        'request_type' => 'get',
                                        'name' => 'grossarea_down',
                                        'label' => 'от',
                                    ]); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?= \app\components\MdbTextInput::widget([
                                        'request_type' => 'get',
                                        'name' => 'grossarea_up',
                                        'label' => 'до',
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-6">
                        <div class='text-center'>Этаж</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?php // выбор периода подачит обьявлений
                                    echo \app\components\MdbSelect::widget([
                                        'request_type' => 'get',
                                        'name' => 'floor_down',
                                        'placeholder' => 'от',
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
                                        'name' => 'floor_up',
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
                    <div class="col-md-6">
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
                </div>
            </div>
            <div class="col-md-1">
                <?= Html::submitButton('<i class="fa fa-refresh" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm']) ?>
               <?= Html::a('<i class="fa fa-floppy-o" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm save-salefilter']) ?>
                <a class="btn btn-primary btn-sm" data-toggle="collapse" href="#collapseadditional"
                   aria-expanded="false"
                   aria-controls="collapseadditional">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </a>
            </div>

        </div>

        <div class="collapse row" id="collapseadditional">
            <div class="row">
                <div class="col-md-3">
                    <div class='text-center'>Год постройки</div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <?= \app\components\MdbTextInput::widget([
                                    'request_type' => 'get',
                                    'name' => 'year_down',
                                    'label' => 'от',
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <?= \app\components\MdbTextInput::widget([
                                    'request_type' => 'get',
                                    'name' => 'year_up',
                                    'label' => 'до',
                                ]); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <div class="md-form form-sm">
                                    <?= \app\components\MdbTextInput::widget([
                                        'request_type' => 'get',
                                        'name' => 'discount',
                                        'label' => '- %',
                                    ]); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class='text-center'>Поиск по телефону</div>
                    <? // echo $form->field($salefilter, 'is_super_filter')->checkbox()->label('Суперфильтр') ?>
                    <div class="md-form form-sm">
                        <?= \app\components\MdbTextInput::widget([
                            'request_type' => 'get',
                            'name' => 'phone',
                            'label' => '',
                        ]); ?>
                    </div>
                </div>
                <div class="col-md-2">

                    <div class="md-form form-sm">
                        <div class='text-center'>Тип дома</div>
                        <?php
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'house_type',
                            'placeholder' => 'от',
                            'options' => \app\models\Sale::HOUSE_TYPES,
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class='text-center'>Поиск по тексту</div>
                    <div class="md-form form-sm">
                        <?= \app\components\MdbTextInput::widget([
                            'request_type' => 'get',
                            'name' => 'text_like',
                            'label' => '',
                        ]); ?>
                    </div>
                    <input type="text" name="polygon_text" id="poly" size="40" hidden
                           value="<? echo $_GET['polygon_text']; ?>">
                    <input type="text" name="plus_tags" id="searching_tags" size="40" hidden
                           value="<? echo $_GET['plus_tags']; ?>">


                </div>
                <div class="col-md-2">
                    <div class="md-form form-sm">
                        <?php // выбор ресурса
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'id_sources',
                            'placeholder' => 'ресурс',
                            'multiple' => 'true',
                            'options' => \app\models\Sale::ID_SOURCES,
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="md-form form-sm">
                        <?php // выбор ресурса
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'sale_disactive',
                            'placeholder' => 'Статус',
                            'options' => [10 => 'Активный', 1 => 'Продано', 2 => 'Пропало'],
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>

                    <div class="md-form form-sm">

                        <?php
                        // выбор ресурса
                        echo \app\components\MdbSelect::widget([
                            'request_type' => 'get',
                            'name' => 'moderated',
                            'value' => $salefilter->moderated,
                            'placeholder' => 'Статус',
                            'options' => [10 => 'Любой статус', 0 => 'нет', 1 => 'да'],
                            'label' => '',
                            'color' => 'primary'
                        ]);
                        ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?php // выбор ресурса
                    echo \app\components\MdbSelect::widget([
                        'request_type' => 'get',
                        'name' => 'not_last_floor',
                        'value' => $salefilter->not_last_floor,
                        'placeholder' => 'последний этаж',
                        'options' => [0 => 'нет', 1 => 'Да'],
                        'label' => 'не последний этаж',
                        'color' => 'primary'
                    ]);
                    ?>
                </div>
                <div class="col-md-1">
                    <?php // выбор ресурса
                    echo \app\components\MdbSelect::widget([
                        'request_type' => 'get',
                        'name' => 'agents',
                        'value' => $salefilter->agents,
                        'placeholder' => 'последний этаж',
                        'options' => [0 => 'нет', 1 => 'Да'],
                        'label' => 'agents',
                        'color' => 'primary'
                    ]);
                    ?>
                </div>
                <div class="col-md-1">
                    <?php // выбор ресурса
                    echo \app\components\MdbSelect::widget([
                        'request_type' => 'get',
                        'name' => 'housekeepers',
                        'value' => $salefilter->housekeepers,
                        'placeholder' => 'последний этаж',
                        'options' => [0 => 'нет', 1 => 'Да'],
                        'label' => 'housekeepers',
                        'color' => 'primary'
                    ]);
                    ?>
                </div>
                <div class="col-md-1">
                    <?php // выбор ресурса
                    echo \app\components\MdbSelect::widget([
                        'request_type' => 'get',
                        'name' => 'unique',
                        'value' => $_GET['unique'],
                        'placeholder' => 'последний этаж',
                        'options' => [0 => 'нет', 1 => 'Да', 2 => 'extra'],
                        'label' => 'Уникальные',
                        'color' => 'primary'
                    ]);
                    ?>
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


                                <? echo $this->render('//tags/form-search-by-tags');

                                ?>




                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-1 align-self-center">
                                    <input type="checkbox" id="savefilter"
                                           name="savefilter" <? if ($_GET['savefilter']) echo "checked"; ?>>
                                    <label for="savefilter"></label>
                                </div>
                                <div class="col-md-9">
                                    <div class="md-form form-sm">
                                        <?= \app\components\MdbTextInput::widget([
                                            'request_type' => 'get',
                                            'name' => 'name',
                                            'label' => 'имя фильтра',
                                        ]); ?>
                                    </div>

                                </div>
                                <div class="col-sm-2">
                                    <?php echo \app\components\MdbSelect::widget([
                                        'request_type' => 'get',
                                        'name' => 'type',
                                        'placeholder' => 'Тип фильтра',
                                        'options' => SaleFilters::TYPE_OF_FILTERS_ARRAY,
                                        'label' => 'Тип фильтра',
                                        'color' => 'primary'
                                    ]); ?>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-1 align-self-center">
                                    <input type="checkbox" id="savelist"
                                           name="savelist" <? if ($_GET['savelist']) echo "checked"; ?>>
                                    <label for="savelist"></label>
                                </div>
                                <div class="col-md-8">
                                    <div class="md-form form-sm">
                                        <?= \app\components\MdbTextInput::widget([
                                            'request_type' => 'get',
                                            'name' => 'salelist_name',
                                            'label' => 'имя списка',
                                        ]); ?>
                                    </div>
                                </div>
                                <div class="col-3">  <?php echo \app\components\MdbSelect::widget([
                                        'request_type' => 'get',
                                        'name' => 'regions',
                                        'id' => 'region_id',
                                        'value' => $_GET['regions'],
                                        'placeholder' => 'Районы',
                                        'options' => [10 => 'Любой'] + \app\models\SaleFilters::getRegions(),
                                        'label' => 'Районы',
                                        'color' => 'primary'
                                    ]); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php
        if ($session->getFlash('ExistedSaleFilter')) {
            ?>

            <div class="modal fade" id="ExistedSaleFilter" tabindex="-1" role="dialog"
                 aria-hidden="true">
                <div class="modal-dialog modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            Фильтр с таким название существует, заменить ?
                            <button class="btn-primary salefilter-confirm-rewrite-button">Заменить
                            </button>
                            <button type="button" class="btn-success" data-dismiss="modal"
                                    aria-label="Close"
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
                            <button class="btn-primary salelist-confirm-rewrite-button">Заменить
                            </button>
                            <button type="button" class="btn-success" data-dismiss="modal"
                                    aria-label="Close"
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


    </form>
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
if ($sales) {
    echo " all = " . count($sales);
    echo " id_addresses = " . count(array_group_by($sales, 'id_address'));
}
?>

</div>


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
        if ($sales) {
            $sales = array_group_by($sales, 'id_address');
            echo \app\models\Renders::YPlacemarks($sales);
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
