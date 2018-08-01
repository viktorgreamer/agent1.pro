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
    <div class="searching_tags_div"></div>
    <div id='available_actions'></div>
    <br>
    <form method="get" id="w0">
        <div class="row no-margin-botton">

            <div class="col-md-3 no-margin-botton">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="id" hidden value="<?= $salefilter->id; ?>">
                        <?php // выбор кол-ва комнат
                        echo Mdb::ActiveSelect($salefilter, 'rooms_count', Sale::ROOMS_COUNT_ARRAY, ['multiple' => true, 'placeholder' => 'Любое', 'div_class' => 'no-margin-botton', 'label' => false]); ?>
                        <?php // выбор периода подачит обьявлений
                        echo Mdb::ActiveSelect($salefilter, 'period_ads', SaleFilters::DEFAULT_PERIODS_ARRAY, ['div_class' => 'no-margin-botton', 'label' => false]); ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <?php echo Mdb::ActiveSelect($salefilter, 'sort_by', SaleFilters::TYPE_OF_SORTING_ARRAY, [
                                'div_class' => 'no-margin-botton',
                                'label' => 'сортировка'
                            ]); ?>
                        </div>
                        <div class="row">
                            <?php
                            echo \app\components\Mdb::ModalBegin([
                                'header' => '', 'class' => 'modal-lg', 'id' => 'plus_search_by_Tags',
                                'button' => [
                                    'class' => 'btn btn-outline-primary btn-rounded waves-effect btn-sm',
                                    'title' => '+'
                                ]
                            ]);
                            ?>

                            <div class="modal-body">
                                <div class="searching_tags_div"></div>
                                <? echo $this->render('//tags/quick-add-form', [
                                    'parent_id' => 0,
                                    'realtags' => \app\models\Tags::convertToArray($salefilter->plus_tags),
                                    'type' => 'plus_search'
                                ]);
                                ?>

                            </div>
                            <?= Mdb::ModalEnd(); ?>

                            <?php
                            echo \app\components\Mdb::ModalBegin([
                                'header' => '', 'class' => 'modal-lg', 'id' => 'minus_search_by_Tags',
                                'button' => [
                                    'class' => 'btn btn-outline-primary btn-rounded waves-effect btn-sm',
                                    'title' => '-'
                                ]
                            ]);
                            ?>
                            <div class="modal-body">
                                <div class="searching_tags_div"></div>
                                <? echo $this->render('//tags/quick-add-form', [
                                    'parent_id' => 0,
                                    'realtags' => \app\models\Tags::convertToArray($salefilter->minus_tags),
                                    'type' => 'minus_search'
                                ]);
                                ?>

                            </div>
                            <?= Mdb::ModalEnd(); ?>

                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-4 no-margin-botton">
                <div class="row">
                    <div class="col-md-6">
                        <div class='text-center'> Цена</div>
                        <div class="row no-margin-botton no-margin-top mdb-select-small-height">
                            <div class="col-md-6 no-margin-botton">
                                <div class="md-form form-sm no-margin-botton">
                                    <?= Mdb::ActiveTextInput($salefilter, 'price_down', ['label' => 'от']); ?>
                                </div>
                            </div>
                            <div class="col-md-6 no-margin-botton">
                                <div class="md-form form-sm no-margin-botton">
                                    <?= Mdb::ActiveTextInput($salefilter, 'price_up', ['label' => 'до']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="md-form form-sm ">
                            <?php echo Mdb::Select('view', '', SaleFilters::TYPE_OF_VIEWS, ['label' => false, 'div_class' => 'no-margin-botton', 'label' => 'Вид']); ?>
                        </div>


                    </div>
                    <div class="col-md-6">
                        <div class='text-center'>Площадь</div>
                        <div class="row no-margin-botton no-margin-top mdb-select-small-height">
                            <div class="col-md-6 no-margin-botton">
                                <div class="md-form form-sm">
                                    <?= Mdb::ActiveTextInput($salefilter, 'grossarea_down', ['label' => 'от']); ?>
                                </div>
                            </div>
                            <div class="col-md-6 no-margin-botton">
                                <div class="md-form form-sm">
                                    <?= Mdb::ActiveTextInput($salefilter, 'grossarea_up', ['label' => 'до']); ?>

                                </div>
                            </div>
                        </div>
                        <div class="md-form form-sm ">
                            <? echo Mdb::ActiveSelect($salefilter, 'regions', [0 => 'Любой'] + SaleFilters::getRegions(), ['div_class' => 'no-margin-botton', 'label' => 'Район']); ?>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-4 no-margin-botton ">
                <div class="row">
                    <div class="col-md-6">
                        <div class='text-center'>Этаж</div>
                        <div class="row no-margin-botton">
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?php echo Mdb::ActiveSelect($salefilter, 'floor_down', Sale::getFloors(), ['label' => 'от']); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?php echo Mdb::ActiveSelect($salefilter, 'floor_up', Sale::getFloors(), ['label' => 'до']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="md-form form-sm">
                            <?php echo Mdb::ActiveSelect($salefilter, 'uniqueness', SaleFilters::TYPE_OF_UNIQUING, ['label' => 'Уникальность']); ?>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class='text-center'>Этажность</div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_down', Sale::getFloors(), ['label' => 'от']); ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="md-form form-sm">
                                    <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_up', Sale::getFloors(), ['label' => 'до']); ?>
                                </div>
                            </div>

                        </div>
                        <div class="md-form form-sm">
                            <?php echo Mdb::ActiveSelect($salefilter, 'house_type', Sale::HOUSE_TYPES, ['label' => 'Тип дома']); ?>
                        </div>

                    </div>

                </div>


            </div>
            <div class="col-md-1 no-margin-botton">
                <?= Html::submitButton('<i class="fa fa-refresh" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm']) ?>
                <?= Html::a('<i class="fa fa-floppy-o" aria-hidden="true"></i>', "#", ['class' => 'btn btn-primary btn-sm save-salefilter', 'id' => 'salefilter-save-button']) ?>
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
                    <div class="row">
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <?= Mdb::ActiveTextInput($salefilter, 'year_down', ['label' => 'от г.п.']); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <?= Mdb::ActiveTextInput($salefilter, 'year_up', ['label' => 'до г.п.']); ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="md-form form-sm">
                                <div class="md-form form-sm">
                                    <?= Mdb::ActiveTextInput($salefilter, 'discount', ['label' => '- %']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="md-form form-sm">
                        <?= Mdb::ActiveTextInput($salefilter, 'phone', ['label' => 'Поиск по телефону']); ?>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="md-form form-sm">
                        <?= Mdb::ActiveTextInput($salefilter, 'text_like', ['label' => 'Поиск по тексту']); ?>
                    </div>
                    <input type="text" name="polygon_text" id="poly" size="40" hidden
                           value="<? echo $_GET['polygon_text']; ?>">
                    <input type="text" name="plus_tags" id="plus_searching_tags" size="40" hidden
                           value="<? echo $_GET['plus_tags']; ?>">
                    <input type="text" name="minus_tags" id="minus_searching_tags" size="40" hidden
                           value="<? echo $_GET['minus_tags']; ?>">


                </div>
                <div class="col-md-2">
                    <?php echo Mdb::ActiveCheckbox($salefilter, 'not_last_floor', ['label' => 'Непосл. этаж']); ?>
                </div>
                <div class="col-md-1">
                    <?php echo Mdb::ActiveCheckbox($salefilter, 'agents', ['label' => 'Агенты']); ?>
                </div>
                <div class="col-md-1">
                    <?php echo Mdb::ActiveCheckbox($salefilter, 'housekeepers', ['label' => 'Хозяин']); ?>
                </div>

            </div>
            <div class="row">

                <div class="col-md-2">
                    <div class=" form-sm">
                        <?php // выбор ресурса
                        echo Mdb::ActiveSelect($salefilter, 'id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой']); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class=" form-sm">
                        <?php echo Mdb::ActiveSelect($salefilter, 'sale_disactive', [10 => 'Любой', 0 => 'Активный', 1 => 'Продано', 2 => 'Пропало', '4' => "РЕАЛЬНЫЕ"], ['label' => 'Статус']); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class=" form-sm">
                        <?php echo Mdb::ActiveSelect($salefilter, 'moderated', [0 => 'Любой статус'] + Sale::TYPE_OF_MODERATED, ['label' => 'moderated']); ?>
                    </div>
                </div>
                <div class="col-md-2">

                </div>
            </div>
            <div class="row">

                <div class="col-md-8">
                    <div class="row">
                        <div class="col-md-8">
                            <div class=" form-sm">
                                <?= Html::activeInput('text', $salefilter, 'name', ['class' =>  'save-filter-name', 'id' => 'sale-filter-name', 'placeholder' => 'имя фильтра' ]) ?>
                                <?  // echo  Mdb::ActiveTextInput($salefilter, 'name', ['class' => 'save-filter-name', 'label' => 'имя фильтра', 'id' => 'sale-filter-name']); ?>
<br>
                               <?= Mdb::ActiveTextInput($salefilter, 'hidden_comment'); ?>

                            </div>

                        </div>
                        <div class="col-sm-4">
                            <? echo Mdb::ActiveSelect($salefilter, 'type', SaleFilters::TYPE_OF_FILTERS_ARRAY, ['id' => 'salefilter-type']); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">

                        <div class="col-md-1 align-self-center">
                            <input type="checkbox" id="savelist"
                                   name="savelist" <? if ($_GET['savelist']) echo "checked"; ?>>
                            <label for="savelist"></label>
                        </div>
                        <div class="col-md-5">
                            <div class="md-form form-sm">
                                <?= \app\components\MdbTextInput::widget([
                                    'request_type' => 'get',
                                    'name' => 'salelist_name',
                                    'label' => 'имя списка',
                                ]); ?>
                            </div>
                        </div>

                    </div>
                    <div class="form-sm">
                        <?php // выбор ресурса
                        echo Mdb::ActiveSelect($salefilter, 'disactive_id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой']); ?>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php

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
    var type = $('#salefilter-type').val();
     $.ajax({
        url: '/sale-filters/save-current-salefilter',
        data: {name: name, type: type},
        type: 'get',
        success: function (data) {
          
            $('#available_actions').html(data);
          
             },
    });
   
});


 




JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
