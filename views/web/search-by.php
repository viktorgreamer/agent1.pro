<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SaleLists;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\Pjax;


$this->title = $salelist->name;
$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['position' => \yii\web\View::POS_HEAD]);
$salelist = SaleLists::findOne($salelist->id);
?>
<div class="row">

    <div class="col-md-8 ">
        <h3>
            <strong><?php echo $salelist->name; ?></strong><br>
                     <small class="text-muted"><?php echo "Описание: " . $salelist->komment; ?></small>
        </h3> <span class="badge badge-pill light-blue"><?= $salelist->getCount(); ?></span> предложений от  <span class="badge badge-pill light-blue">
            <?= number_format($salelist->getMinPrice(),0, ".", "."); ?>
        </span> до
        <span class="badge badge-pill light-blue">
            <?= number_format($salelist->getMaxPrice(),0, ".", "."); ?></span> Руб.
        <div class="row justify-content-end" style="padding-right:15px;">
            <p class="h6 text-info">
                Обновлено <?= Yii::$app->formatter->asRelativeTime($salelist->timestamp); ?></p>
        </div>
        <button id="#regionset" type="button" class="btn btn-primary regionset"
                data-toggle="modal"
                data-target="#myModal">Посмотреть на карте
            <? // if ($salefilter->polygon_text != '') echo "<i class=\"fa fa-map-marker fa-inverse fa-2x\" aria-hidden=\"true\" style=\"color:green\"></i>";
            //else echo "<i class=\"fa fa-map-marker fa-2x\" aria-hidden=\"true\"></i>"; ?> </button>

        <?php echo \app\components\SaleTableWidgets::widget([
            'salelist' => $salelist,
            'sales' => $data,
            'type' => 'web'
        ]); ?>
        <?php echo \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            'options' => ['class' => 'pagination pagination-circle pg-blue mb-0'],
            'linkOptions' => ['class' => 'page-link'],
            'firstPageCssClass' => 'page-item first',
            'prevPageCssClass' => 'page-item last',
            'nextPageCssClass' => 'page-item next',
            'activePageCssClass' => 'page-item active',
            'disabledPageCssClass' => 'page-item disabled'
        ]);
        ?>

    </div>
    <div class="col-md-4">
            <a href="#" type="button" class="badge badge-danger animated flash " style="font-size: larger" data-toggle="modal"
               data-target="#modalContactForm"> Оставить заявку на просмотр </a>

        <h3>Смотрите также:</h3>
        <?php
        $salelists = SaleLists::find()->where(['in', 'id', $salelist->getRelevantedByTags(5)])->andWhere(['<>', 'id', $salelist->id])->all();
        foreach ($salelists as $salelist1) {
            $salelist1 = \app\models\SaleLists::findOne($salelist1->id);
            ?>
            <div class="row">
                <div class="col-12">

                    <a href="<?= \yii\helpers\Url::to(['web/search-by', 'id' => $salelist1->id]); ?>">
                        <h5><span class="badge cyan animated pulse "><?php echo $salelist1->name; ?> </span></h5>
                    </a>
                    <h6><?php echo $salelist1->komment; ?></h6>
                </div>
            </div>
            <div class="row">
                <div class="col-9">
                    от <?php echo $salelist1->getMinPrice(); ?>
                </div>
                <div class="col-3"><span
                            class="badge badge-pill pink"><?php echo $salelist1->getCount(); ?>
                        ШТ.</span>
                </div>

            </div>

            <?
        }
        ?>
        <div class="row">
            <?= \app\components\webWidgets\AdvicesWidgets::widget(['count' => 4]); ?>
        </div>

    </div>
</div>

<!--Modal: Contact form-->
<div class="modal fade" id="modalContactForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog cascading-modal" role="document">
        <!--Content-->
        <div class="modal-content">

            <!--Header-->
            <div class="modal-header light-blue darken-3 white-text">
                <h4 class="title"><i class="fa fa-pencil"></i>Контактная форма
                    <button type="button" class="close waves-effect waves-light" data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
            </div>
            <!--Body-->
            <form action="<?= \yii\helpers\Url::to(["web/search-by?id=".$_GET['id']]) ?>" method="post">
                <div class="modal-body mb-0">
                    <div class="md-form form-sm">
                        <i class="fa fa-user prefix"></i>
                        <input type="text" name="name" id="form19" class="form-control">
                        <label for="form19">Как к вам обращаться?</label>
                    </div>

                    <div class="md-form form-sm">
                        <i class="fa fa-phone prefix"></i>
                        <input type="text" id="form21" name="phone" class="form-control">
                        <label for="form21">Телефон</label>
                    </div>

                    <div class="md-form form-sm">
                        <i class="fa fa-pencil prefix"></i>
                        <textarea type="text" id="form8" name="description" class="md-textarea mb-0"></textarea>
                        <label for="form8">Описание вашей заявки</label>
                    </div>

                    <div class="text-center mt-1-half">
                        <button class="btn btn-info mb-2" type="submit">Оправить<i class="fa fa-send ml-1"></i>
                        </button>
                    </div>
                </div>
            </form>

        </div>
        <!--/.Content-->
    </div>
</div>
<!--Modal: Contact form-->


<!-- Central Modal Medium Success -->
<div class="modal fade" id="centralModalSuccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-side modal-bottom-right modal-notify modal-info" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header">
                <p class="heading lead"></p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">&times;</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body">
                <div class="text-center">
                    <i class="fa fa-check fa-2x mb-3 animated rotateIn"></i>
                    <h3>Хотите оформим заявку по вашим уникальным требованиям?</h3>
                </div>
            </div>

            <!--Footer-->
            <div class="modal-footer justify-content-center">
                <a type="button" class="btn btn-primary-modal" data-toggle="modal" data-target="#modalContactForm"
                   data-dismiss="modal">Да, я хочу!</a>

                <a type="button" class="btn btn-outline-secondary-modal waves-effect" data-dismiss="modal">Спасибо,
                    не надо.</a>
            </div>
        </div>
        <!--/.Content-->
    </div>
</div>
<!-- Central Modal Medium Success-->
<?php
$session = Yii::$app->session;
echo $this->render('_counters');

if ($session->getFlash('order_get')) {
    $script = <<<JS
toastr["info"]("Заявка успешно отправлена, ожидайте звонка в ближайщие несколько минут!");
JS;
    $this->registerJs($script, yii\web\View::POS_READY);

} else {
    if (!$session->get('contact-form-search-by-notice')) {
        $JS = <<<JS
function showModal() {
    $('#centralModalSuccess').modal('show');
    $.ajax({
        url: '/web/set-contact-form-shown?q=sb',
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
}
setTimeout(showModal, 15000);
JS;
        $this->registerJs($JS, \yii\web\View::POS_READY);
    }

}
?>


<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><strong><?php echo $salelist->name; ?></strong> </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">


                <div id="map" style="width:100%; height: 500px">

                </div>
            </div>
        </div>
    </div>
</div>
<?
$prefix = \app\models\Sale::$tablePrefix;
$module = \app\models\Control::find()->where(['region' => $prefix])->one();
if (isset($_SESSION['coords_x'])) $y_maps_coords_X = $_SESSION['coords_x'];
else $y_maps_coords_X = $module->coords_x;
if (isset($_SESSION['coords_y'])) $y_maps_coords_Y = $_SESSION['coords_y'];
else $y_maps_coords_Y = $module->coords_y;
$zoom = $module->zoom * 1.2;
?>


<script type="text/javascript">
    // Как только будет загружен API и готов DOM, выполняем инициализацию
    ymaps.ready(init);

    function init() {
        var myMap = new ymaps.Map("map", {
            center: [<?=  $y_maps_coords_X ?> , <?=  $y_maps_coords_Y ?>],
            zoom: <?= $zoom; ?>,
            controls: ['zoomControl', 'typeSelector', 'fullscreenControl']
        });
        polygon = new ymaps.GeoObject({
            geometry: {
                type: "Polygon",
                coordinates: <? if (!empty($salefilter->polygon_text)) echo $salefilter->polygon_text; else echo "[]"; ?>
            }

        });

        <?
        if ($data) {
            $data = array_group_by($data, 'id_address');
            echo \app\models\Renders::YPlacemarks($data,'public');
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

