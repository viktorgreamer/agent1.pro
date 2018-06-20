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
/* @var $model app\models\SaleFilters */
/* @var $salefilter app\models\SaleFilters */
/* @var $salefilters app\models\SaleFilters */
/* @var $form ActiveForm */
$session = Yii::$app->session;
$module = $session->get('module');


?>

<!-- Switch -->


<div class="sale-index">
    <div class="searching_tags_div"></div>
    <br>
    <div class="row no-margin-botton">

        <div class="col-md-3 no-margin-botton">
            <div class="row">
                <div class="col-md-6">
                    <?php // выбор кол-ва комнат
                    echo Mdb::ActiveSelect($salefilter, 'rooms_count', \app\models\Sale::ROOMS_COUNT_ARRAY, ['multiple' => true, 'placeholder' => 'Любое', 'div_class' => 'no-margin-botton']); ?>
                    <?php // выбор периода подачит обьявлений
                    echo Mdb::ActiveSelect($salefilter, 'period_ads', \app\models\SaleFilters::DEFAULT_PERIODS_ARRAY, ['div_class' => 'no-margin-botton']); ?>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <?php echo Mdb::Select('sort_by', 'сортировка', [2 => 'цена', 1 => 'время', 3 => 'адресам'], ['div_class' => 'no-margin-botton']); ?>
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
                        <?php echo Mdb::Select('view', '', [1 => 'Список', 2 => 'Карта'], ['label' => false, 'div_class' => 'no-margin-botton', 'label' => 'Вид']); ?>
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
                        <? echo Mdb::ActiveSelect($salefilter, 'regions', [0 => 'Любой'] + \app\models\SaleFilters::getRegions(), ['div_class' => 'no-margin-botton', 'label' => 'Район']); ?>
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
                                <?php echo Mdb::ActiveSelect($salefilter, 'floor_down', \app\models\Sale::getFloors(), ['label' => 'от']); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm">
                                <?php echo Mdb::ActiveSelect($salefilter, 'floor_up', \app\models\Sale::getFloors(), ['label' => 'до']); ?>
                            </div>
                        </div>
                    </div>
                    <div class="md-form form-sm">
                        <?php echo Mdb::Select('unique', 'Уникальсть', [0 => 'нет', 1 => 'Да', 2 => 'extra']); ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class='text-center'>Этажность</div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="md-form form-sm">
                                <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_down', \app\models\Sale::getFloors(), ['label' => 'от']); ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="md-form form-sm">
                                <?php echo Mdb::ActiveSelect($salefilter, 'floorcount_up', \app\models\Sale::getFloors(), ['label' => 'до']); ?>
                            </div>
                        </div>

                    </div>
                    <div class="md-form form-sm">
                        <?php echo Mdb::ActiveSelect($salefilter, 'house_type', \app\models\Sale::HOUSE_TYPES, ['label' => 'Тип дома']); ?>
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
                       value="<? echo $_POST['polygon_text']; ?>">
                <input type="text" name="plus_tags" id="plus_searching_tags" size="40" hidden
                       value="<? echo $_POST['plus_tags']; ?>">
                <input type="text" name="minus_tags" id="minus_searching_tags" size="40" hidden
                       value="<? echo $_POST['minus_tags']; ?>">


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
                <div class="md-form form-sm">
                    <?php // выбор ресурса
                    echo Mdb::ActiveSelect($salefilter, 'id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой']); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="md-form form-sm">
                    <?php echo Mdb::ActiveSelect($salefilter, 'sale_disactive', [10 => 'Любой', 0 => 'Активный', 1 => 'Продано', 2 => 'Пропало', '4' => "РЕАЛЬНЫЕ"], ['label' => 'Статус']); ?>
                </div>
            </div>
            <div class="col-md-2">
                <div class="md-form form-sm">
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
                        <div class="md-form form-sm">
                            <?= Mdb::ActiveTextInput($salefilter, 'name', ['class' => 'save-filter-name', 'label' => 'имя фильтра', 'id' => 'sale-filter-name']); ?>

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
                <div class="md-form form-sm">
                    <?php // выбор ресурса
                    echo Mdb::ActiveSelect($salefilter, 'disactive_id_sources', \app\models\Sale::ID_SOURCES, ['multiple' => true, 'placeholder' => 'Любой']); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-lg-6">
        <h3>Похожие Фильтры</h3>
        <?php
        $salefilters = SaleFilters::find()->where(['type' => SaleFilters::PUBLIC_TYPE])->andWhere(['<>', 'id', $salefilter->id])->all();
        foreach ($salefilters as $_salefilter) {
            ?>
            <div class="row">
                <h5><a class="badge cyan toggle-relevanted-ids" data-salefilter_id="<?php echo $salefilter->id; ?>"
                       data-similar_id="<?php echo $_salefilter->id; ?>"><?php echo $_salefilter->name; ?> </a></h5>
            </div>


            <?
        } ?>

    </div>
    <div class="col-lg-6">
        <h3>Выбранные Фильтры</h3>
        <?php
        if ($salefilter->relevanted_ids) {
            $existed_filters = SaleFilters::find()->where(['in', 'id', explode(",", $salefilter->relevanted_ids)])->all();
            foreach ($existed_filters as $_salefilter) {

                ?>
                <div class="row">
                    <h5><a href="#" class="badge success-color toggle-relevanted-ids"
                           data-salefilter_id="<?php echo $salefilter->id; ?>"
                           data-similar_id="<?php echo $_salefilter->id; ?>"><?php echo $_salefilter->name; ?> </a></h5>
                </div>
            <? }
        }
        ?>
    </div>
</div>


<?php

// проверко что фильтр с таким иеменем сущесввует если да то выводим уведомление и иконку
$script = <<< JS
$('.searching_tags_div').load(encodeURI('/tags/render-tags'));


$('#show-on-map').on('click', function (e) {
 $.ajax({
url: '/sale/show-on-map',
//data: {},
type: 'get',
success: function (data) {

 }
});
});

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
var type = $("input[name='type']").val();

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
    var type = $("input[name='type']").val();
     $.ajax({
        url: '/sale-filters/save-current-salefilter',
        data: {name: name, type: type},
        type: 'get',
        success: function (data) {
          
            $('#available_actions').html(data);
          
             },
    });
});


// добавление или удаление текущего tags для salelists
$('.toggle-relevanted-ids').on('click', function (e) {
    e.preventDefault();
    var salefilter_id = $(this).data('salefilter_id');
    var similar_id = $(this).data('similar_id');

    $.ajax({
        url: '/sale-filters/toggle-relevanted-ids',
        data: {salefilter_id: salefilter_id, similar_id: similar_id},
        type: 'get',
        success: function (res) {

        },

        error: function () {
            alert('error')
        }
    });
    this.disabled = true;
});



JS;
$this->registerJs($script, yii\web\View::POS_READY);
?>
