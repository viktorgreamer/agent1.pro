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
use app\components\Mdb;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $salefilter app\models\SaleFilters */
/* @var $form ActiveForm */

$session = Yii::$app->session;
$module = $session->get('module');

$session->set('tags_id_to_search_sale', $salefilter->plus_tags);
$session->set('minus_tags_id_to_search_sale', $salefilter->minus_tags);
?>

<!-- Switch -->
<div class="sale-index">
    <form method="get" id="w0">
        <div class="row" style="height: 55px;">
            <div class="searching_tags_div col-md-8 col-lg-9 col-sm-10 col-10 pr-0 mr-0"></div>
            <div class="col-md-2 col-lg-2 col-sm-2 col-2 m-0 p-0">
                <div class="row">
                    <div class=" col-lg-8 m-0 p-0">
                        <div class="switch primary-switch">
                        <label class="m-0 p-0 text-primary">
                            <?= Html::img('/web/icons/list.png',['height' => '25px','title' => 'Список']); ?>
                            <?php echo Html::checkbox( 'view',$_GET['view']); ?>
                            <span class="lever m-1"></span>
                            <?= Html::img('/web/icons/map.png',['height' => '25px','title' => 'Карта']); ?>
                        </label>
                        </div>
                        <div class="switch primary-switch">
                            <label class="m-0 p-0 text-primary">
                                <?= Html::img('/web/icons/copy.png',['height' => '25px','title' => 'Все']); ?>
                                <?php echo Html::checkbox('uniqueness',$salefilter->uniqueness); ?>

                                <span class="lever m-1"></span>
                                <?= Html::img('/web/icons/unique.png',['height' => '25px','title' => 'Уникальные']); ?>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-4 col-sm-6 col-6 m-0 p-0">
                        <?php echo Mdb::ActiveSelect($salefilter, 'sort_by', SaleFilters::TYPE_OF_SORTING_ARRAY, [
                            'div_class' => 'no-margin-botton',
                            'label' => 'сортировка'
                        ]); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-2 col-lg-1 col-sm-2 col-2">
                <?= Html::submitButton('<i class="fa fa-search mirs_preloader " aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm btn-rounded']) ?>
            </div>
        </div>
        <!--Accordion wrapper-->
        <div class="accordion" id="accordionEx" role="tablist" aria-multiselectable="true">

            <input type="text" name="id" hidden value="<?= $salefilter->id; ?>">
            <input type="text" name="polygon_text" id="poly" size="40" hidden
                   value="<? echo $_GET['polygon_text']; ?>">
            <input type="text" name="plus_tags" id="plus_searching_tags" size="40" hidden
                   value="<? echo $_GET['plus_tags']; ?>">
            <input type="text" name="minus_tags" id="minus_searching_tags" size="40" hidden
                   value="<? echo $_GET['minus_tags']; ?>">
            <div class="card">
                <!-- Card header -->
                <div class="card-header"
                     style="padding-left: 0px;padding-right: 0px; padding-bottom: 5px;padding-top: 5px;" role="tab"
                     id="headingMain">
                    <a data-toggle="collapse" href="#collapseMain" aria-expanded="true" aria-controls="collapseMain">
                        <h5 class="mb-0">Основные<i class="fa fa-angle-down rotate-icon"></i></h5>
                    </a>
                </div>
                <hr class="m-0 p-0">
                <div id="collapseMain" class="collapse" role="tabpanel" aria-labelledby="headingMain"
                     data-parent="#accordionEx">
                    <div class="card-body">
                        <div class="row no-margin-botton">
                            <div class="col-md-2 col-lg-1 col-sm-6 col-6 m-0 p-0">
                                       <?php // выбор кол-ва комнат
                                        echo Mdb::ActiveSelect($salefilter, 'rooms_count', Sale::ROOMS_COUNT_ARRAY, ['multiple' => true, 'placeholder' => 'Любое', 'div_class' => 'no-margin-botton', 'label' => 'комнат']); ?>
                             </div>
                            <div class="col-md-4 col-lg-3 col-sm-12 col-12 m-0 p-0">
                                <div class="row">
                                    <div class="col-md-12 col-sm-2 col-2 col-lg-2  m-0 p-0">
                                        <i class="fa fa-rub col-lg-1 fa-2x" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-12 col-sm-5 col-4 col-lg-3 m-0 p-0 align-bottom">
                                        <div class="form-sm m-0 p-0">
                                            <?= Mdb::ActiveTextInput($salefilter, 'price_down', ['label' => 'от']); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 m-0 p-0"></div>
                                    <div class="col-md-12 col-sm-5 col-4 col-lg-3 m-0 p-0">
                                        <div class="form-sm m-0 p-0">
                                            <?= Mdb::ActiveTextInput($salefilter, 'price_up', ['label' => 'до']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-lg-2 col-sm-12 col-12 m-0 p-0">
                                <div class="row">
                                    <div class="col-lg-1 m-0 p-0"></div>
                                    <div class="col-md-4 col-sm-2 col-2 col-lg-2  m-0 p-0">
                                        <?= Html::img('/web/icons/s2.png',['height' => '32px']); ?>

                                    </div>
                                    <div class="col-md-4 col-sm-5 col-5 col-lg-3 m-0 p-0">
                                        <div class="md-form form-sm p-0 m-0">
                                            <?= Mdb::ActiveTextInput($salefilter, 'grossarea_down', ['label' => 'от']); ?>
                                        </div>
                                    </div>
                                    <div class="col-lg-1 m-0 p-0"></div>

                                    <div class="col-md-4 col-sm-5 col-5 col-lg-3 m-0 p-0">
                                        <div class="md-form form-sm p-0 m-0">
                                            <?= Mdb::ActiveTextInput($salefilter, 'grossarea_up', ['label' => 'до']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                <div class="row">
                                    <div class="col-md-2 col-lg-2 col-sm-2 col-2 m-0 p-0" >
                                        <?php
                                        echo \app\components\Mdb::ModalBegin([
                                            'header' => '<div class="searching_tags_div"></div>',
                                            'class' => 'modal-lg', 'id' => 'plus_search_by_Tags',
                                            'button' => [
                                                'class' => 'btn-floating cyan waves-effect waves-light',
                                                'title' => "<i class=\"fa fa-tags fa-2x\" aria-hidden=\"true\"></i>"
                                            ]
                                        ]);
                                        ?>

                                        <div class="modal-body">
                                            <? echo $this->render('//tags/quick-add-form-alternative', [
                                                'parent_id' => 0,
                                                'realtags' => \app\models\Tags::convertToArray($salefilter->plus_tags),
                                                'type' => 'plus_search',
                                                'searchable' => true,
                                            ]);
                                            ?>
                                        </div>
                                        <?= Mdb::ModalEnd(); ?>
                                    </div>
                                    <div class="col-md-2 col-lg-2 col-sm-2 col-2 m-0 p-0">
                                        <?php
                                        echo \app\components\Mdb::ModalBegin([
                                            'header' => '<div class="searching_tags_div"></div>'
                                            , 'class' => 'modal-lg', 'id' => 'minus_search_by_Tags',
                                            'button' => [
                                                'class' => 'btn-floating red waves-effect waves-light',
                                                'title' => "<i class=\"fa fa-tags fa-2x\" aria-hidden=\"true\"></i>"
                                            ]
                                        ]);
                                        ?>
                                        <div class="modal-body">
                                            <? echo $this->render('//tags/quick-add-form-alternative', [
                                                'parent_id' => 0,
                                                'realtags' => \app\models\Tags::convertToArray($salefilter->minus_tags),
                                                'type' => 'minus_search',
                                                'searchable' => true,
                                            ]);
                                            ?>
                                        </div>
                                        <?= Mdb::ModalEnd(); ?>
                                    </div>

                                    <div class="col-md-8 col-sm-8 col-lg-8 col-8">
                                        <div class="md-form form-sm ">
                                            <? echo Mdb::ActiveSelect($salefilter, 'regions', [0 => 'Любой'] + SaleFilters::getRegions(), ['div_class' => 'no-margin-botton', 'label' => 'Район']); ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2 col-lg-1 col-sm-2 col-2">
                                <?php echo Mdb::ActiveSelect($salefilter, 'balcon', SaleFilters::mapBalcon(), ['label' => 'Балк/Лодж']); ?>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Card header -->
                <div class="card-header"
                     style="padding-left: 0px;padding-right: 0px;  padding-bottom: 5px;padding-top: 5px;" role="tab"
                     id="headingOne">
                    <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        <h5 class="mb-0">Дом<i class="fa fa-angle-down rotate-icon"></i>
                        </h5>
                    </a>
                </div>
                <hr class="m-0 p-0">
                <!-- Card body -->
                <div id="collapseOne" class="collapse" role="tabpanel" aria-labelledby="headingOne"
                     data-parent="#accordionEx">
                    <div class="card-body">
                        <div class="row no-margin-botton">
                            <div class="col-md-6 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                <div class="row">
                                    <div class="col-md-2 col-sm-2 col-2">
                                        <i class="fa fa-building-o fa-2x" aria-hidden="true"></i>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-5">
                                        <?= Mdb::ActiveTextInput($salefilter, 'year_down', ['label' => 'от']); ?>
                                    </div>
                                    <div class="col-md-5 col-sm-5 col-5">
                                        <?= Mdb::ActiveTextInput($salefilter, 'year_up', ['label' => 'до']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-4">
                                        Этаж
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-4">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'floor_down', Sale::getFloors(), ['label' => 'от']); ?>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-4">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'floor_up', Sale::getFloors(), ['label' => 'до']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                <div class="row">
                                    <div class="col-md-4 col-sm-4 col-4">
                                        Этажн.
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-4">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_down', Sale::getFloors(), ['label' => 'от']); ?>
                                    </div>
                                    <div class="col-md-4 col-sm-4 col-4">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_up', Sale::getFloors(), ['label' => 'до']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                <div class="row">
                                    <div class="col-md-6 col-sm-6 col-6">

                                        <?php echo Mdb::ActiveCheckbox($salefilter, 'not_last_floor', ['label' => '<small>Непосл. этаж</small>']); ?>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-6">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'house_type', Sale::HOUSE_TYPES, ['label' => 'Тип дома']); ?>
                                    </div>
                                    <div class="col-md-6 col-sm-6 col-6">
                                        <?php echo Mdb::ActiveSelect($salefilter, 'person_type', \app\models\Agents::mapPersonType(), ['label' => 'агент\хозяин', 'id' => 'person_type']); ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card header -->

                <?php if (Yii::$app->user->can(\app\models\User::ROLE_PLAN1)) { ?>
                    <div class="card-header"
                         style="padding-left: 0px;padding-right: 0px;  padding-bottom: 5px;padding-top: 5px;" role="tab"
                         id="headingExtended">
                        <a data-toggle="collapse" href="#Extended" aria-expanded="true" aria-controls="Extended">
                            <h5 class="mb-0">Расширенные<i class="fa fa-angle-down rotate-icon"></i>
                            </h5>
                        </a>
                    </div>
                    <hr class="m-0 p-0">
                    <!-- Card body -->
                    <div id="Extended" class="collapse" role="tabpanel" aria-labelledby="headingExtended"
                         data-parent="#accordionEx">
                        <div class="card-body">
                            <div class="row no-margin-botton">
                                <div class="col-md-3 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?php echo Mdb::ActiveSelect($salefilter, 'id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой', 'label' => 'Источник']); ?>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?php echo Mdb::ActiveSelect($salefilter, 'sale_disactive', [10 => 'Любой', 0 => 'Активный', 1 => 'Продано', 2 => 'Пропало', '4' => "РЕАЛЬНЫЕ"], ['label' => 'Статус']); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?php echo Mdb::ActiveSelect($salefilter, 'moderated', [0 => 'Любой статус'] + Sale::TYPE_OF_MODERATED, ['label' => 'Модерация']); ?>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?php // выбор ресурса
                                            echo Mdb::ActiveSelect($salefilter, 'disactive_id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой', ['label' => 'Отсутствует']]); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?php // выбор периода подачит обьявлений
                                            echo Mdb::ActiveSelect($salefilter, 'period_ads', SaleFilters::DEFAULT_PERIODS_ARRAY, ['div_class' => 'no-margin-botton', 'label' => false]); ?>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-6">
                                            <?= Mdb::ActiveTextInput($salefilter, 'discount', ['label' => '- %']); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-lg-3 col-sm-12 col-12 no-margin-botton">
                                    <div class="row">
                                        <?= Mdb::ActiveTextInput($salefilter, 'phone', ['label' => 'Поиск по телефону']); ?>
                                    </div>
                                    <div class="row">
                                        <?= Mdb::ActiveTextInput($salefilter, 'text_like', ['label' => 'Поиск по тексту']); ?>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="card-header"
                         style="padding-left: 0px;padding-right: 0px; padding-bottom: 5px;padding-top: 5px;" role="tab"
                         id="headingSave">
                        <a data-toggle="collapse" href="#collapseSave" aria-expanded="true"
                           aria-controls="collapseSave">
                            <h5 class="mb-0">Сохранить<i class="fa fa-angle-down rotate-icon"></i>
                            </h5>
                        </a>
                    </div>
                    <div id="collapseSave" class="collapse" role="tabpanel" aria-labelledby="headingSave"
                         data-parent="#accordionEx">
                        <div class="card-body">
                            <div class="row no-margin-botton">
                                <div class="col-md-6 col-lg-6 col-sm-12 col-12 no-margin-botton">
                                    <div class="row">
                                        <div class="col-md-8 col-sm-8 col-8">
                                            <? echo Mdb::ActiveSelect($salefilter, 'type', SaleFilters::TYPE_OF_FILTERS_ARRAY, ['id' => 'salefilter-type']); ?>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-4">
                                            <?= Html::button('<i class="fa fa-floppy-o" aria-hidden="true"></i>', ['class' => CLASS_BUTTON . " save-salefilter", 'id' => 'salefilter-save-button']) ?>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-12">
                                            <?= Html::activeInput('text', $salefilter, 'name', ['class' => 'save-filter-name', 'id' => 'sale-filter-name', 'placeholder' => 'имя фильтра']) ?>
                                        </div>
                                        <div class="col-md-12 col-sm-12 col-12">
                                            <?= Mdb::ActiveTextInput($salefilter, 'hidden_comment'); ?>    </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-6 col-sm-12 col-12 no-margin-botton">
                                    <h3> Посылать обновления</h3>
                                    <div class="row">
                                        <div class="col-md-4 col-sm-4 col-4">
                                            <?php echo Mdb::ActiveCheckbox($salefilter, 'mail_inform', ['label' => 'E-mail']); ?>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-4">
                                            <?php echo Mdb::ActiveCheckbox($salefilter, 'vk_inform', ['label' => 'Vkontakte']); ?>
                                        </div>
                                        <div class="col-md-4 col-sm-4 col-4">
                                            <?php echo Mdb::ActiveCheckbox($salefilter, 'sms_inform', ['label' => 'Sms']); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>
                <?php } ?>


            </div>
        </div>
    </form>
</div>

<?php
$csrf = Yii::$app->request->csrfToken;

// проверко что фильтр с таким иеменем сущесввует если да то выводим уведомление и иконку
$script = <<< JS
$('.searching_tags_div').load(encodeURI('/tags/render-tags'));


// $('#show-on-map').on('click', function (e) {
//  $.ajax({
// url: '/sale/show-on-map',
// //data: {},
// type: 'get',
// success: function (data) {
//
//  }
// });
// });

var name = $('#sale-filter-name').val();

$.ajax({
url: '/sale-filters/is-existed-name-salefilter',
data: {name: name},
type: 'get',
success: function (data) {
if (data != 'NO')  $('#salefilter-save-button').addClass('red');
 }
});
// добавление класса red если фильтр с таким именем уже существует если в поле с именем фильтра есть измемения
$('#sale-filter-name').change(function () {
var name = $('#sale-filter-name').val();
var type = $('#salefilter-type').val()

$.ajax({
url: '/sale-filters/is-existed-name-salefilter',
data: {name: name, type: type},
type: 'get',
success: function (data) {
            if (data != 'NO')  {
                $('#salefilter-save-button').addClass('red');
                toastr.error(data, '', {timeOut: 5000});
            }
            else {
                $('#salefilter-save-button').removeClass('red');
               // toastr.success(data, '', {timeOut: 2000});
            }
        },
});

});

// если нажата кнопка Сохранить фильтр

$('.save-salefilter').on('click', function (e) {
    e.preventDefault();
    name = $('#sale-filter-name').val();
    type = $('#salefilter-type').val();
    vk_inform = $("[name='vk_inform']").is(':checked');
    mail_inform = $("[name='mail_inform']").is(':checked');
    sms_inform = $("[name='sms_inform']").is(':checked');
    hidden_comment = $("[name='hidden_comment']").val();
    params = {"vk_inform":vk_inform,"mail_inform":mail_inform,"hidden_comment":hidden_comment};
    console.log(params);
     $.ajax({
     type: 'POST',
        url: '/sale-filters/save-current-salefilter',
        data: {name: name, type: type,params:JSON.stringify(params)},
        type: 'get',
        success: function (data) {
          
            $('#available_actions').html(data);
          
             },
    });
   
});


 




JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
