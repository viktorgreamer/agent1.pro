<?php
use yii\helpers\Html;


?>
<div class="row">

    <div class="col-md-3">
        <div class="row">
            <div class="col-md-6">

                <?php // выбор кол-ва комнат
                echo \app\components\MdbSelect::widget([
                    'request_type' => 'post',
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
                    'request_type' => 'post',
                    'name' => 'period_ads',
                    'value' => $salefilter->period_ads,
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
                        'request_type' => 'post',
                        'name' => 'sort_by',
                        'value' => $salefilter->sort_by,
                        'placeholder' => 'до',
                        'options' => [0 => 'время', 1 => 'цена'],
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
                                'request_type' => 'post',
                                'value' => $salefilter->price_down,
                                'name' => 'price_down',
                                'label' => 'от',
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="md-form form-sm">
                            <?= \app\components\MdbTextInput::widget([
                                'request_type' => 'post',
                                'value' => $salefilter->price_up,
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
                                'request_type' => 'post',
                                'value' => $salefilter->grossarea_down,
                                'name' => 'grossarea_down',
                                'label' => 'от',
                            ]); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="md-form form-sm">
                            <?= \app\components\MdbTextInput::widget([
                                'request_type' => 'post',
                                'name' => 'grossarea_up',
                                'value' => $salefilter->grossarea_up,
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
                                'request_type' => 'post',
                                'name' => 'floor_down',
                                'value' => $salefilter->floor_down,
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
                                'request_type' => 'post',
                                'name' => 'floor_up',
                                'value' => $salefilter->floor_up,
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
                                'request_type' => 'post',
                                'name' => 'floorcount_down',
                                'value' => $salefilter->floorcount_down,
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
                                'request_type' => 'post',
                                'name' => 'floorcount_up',
                                'value' => $salefilter->floorcount_up,
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
        <?= Html::submitButton('<i class="fa fa-save" aria-hidden="true"></i>', ['class' => 'btn btn-primary btn-sm']) ?>

    </div>

    <div class="row">
        <div class="col-md-3">
            <div class='text-center'>Год постройки</div>
            <div class="row">
                <div class="col-md-4">
                    <div class="md-form form-sm">
                        <?= \app\components\MdbTextInput::widget([
                            'request_type' => 'post',
                            'name' => 'year_down',
                            'value' => $salefilter->year_down,
                            'label' => 'от',
                        ]); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="md-form form-sm">
                        <?= \app\components\MdbTextInput::widget([
                            'request_type' => 'post',
                            'name' => 'year_up',
                            'value' => $salefilter->year_up,
                            'label' => 'до',
                        ]); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="md-form form-sm">
                        <div class="md-form form-sm">
                            <?= \app\components\MdbTextInput::widget([
                                'request_type' => 'post',
                                'name' => 'discount',
                                'value' => $salefilter->discount,
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
                    'request_type' => 'post',
                    'name' => 'phone',
                    'value' => $salefilter->phone,
                    'label' => '',
                ]); ?>
            </div>
        </div>
        <div class="col-md-2">

            <div class="md-form form-sm">
                <div class='text-center'>Тип дома</div>
                <?php
                echo \app\components\MdbSelect::widget([
                    'request_type' => 'post',
                    'name' => 'house_type',
                    'value' => $salefilter->house_type,
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
                    'request_type' => 'post',
                    'value' => $salefilter->text_like,
                    'name' => 'text_like',
                    'label' => '',
                ]); ?>
            </div>
            <input type="text" name="polygon_text" id="poly" size="40" hidden
                   value="<? echo $salefilter->polygon_text; ?>">


        </div>
        <div class="col-md-2">
            <div class="md-form form-sm">
                <?php // выбор ресурса
                echo \app\components\MdbSelect::widget([
                    'request_type' => 'post',
                    'name' => 'id_sources',
                    'value' => $salefilter->id_sources,
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
                    'request_type' => 'post',
                    'value' => $salefilter->sale_disactive,
                    'name' => 'sale_disactive',
                    'placeholder' => 'Статус',
                    'options' => [0 => 'Любой', 1 => 'Продано', 2 => 'Пропало'],
                    'label' => '',
                    'color' => 'primary'
                ]);
                ?>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-2">
        <?php // выбор ресурса
        echo \app\components\MdbSelect::widget([
            'request_type' => 'post',
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
            'request_type' => 'post',
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
            'request_type' => 'post',
            'name' => 'housekeepers',
            'value' => $salefilter->housekeepers,
            'placeholder' => 'последний этаж',
            'options' => [0 => 'нет', 1 => 'Да'],
            'label' => 'housekeepers',
            'color' => 'primary'
        ]);
        ?>

            Прошел модерацию
            <?php // выбор ресурса
            echo \app\components\MdbSelect::widget([
                'request_type' => 'get',
                'name' => 'moderated',
                'value'=> $salefilter->moderated,
                'placeholder' => 'Статус',
                'options' => [10 => 'Любой статус', 0 => 'нет', 1 => 'да'],
                'label' => '',
                'color' => 'primary'
            ]);
            ?>

    </div>
    <div class="col-md-1">
        <?php // выбор ресурса
        echo \app\components\MdbSelect::widget([
            'request_type' => 'post',
            'name' => 'unique',
            'value' => $_GET['unique'],
            'placeholder' => 'уникальные',
            'options' => [0 => 'нет', 1 => 'Да',2 => 'extra'],
            'label' => 'Уникальные',
            'color' => 'primary'
        ]);
        ?>
    </div>
    <div class="col-md-6">
        <div class="md-form form-sm">
            <?= \app\components\MdbTextInput::widget([
                'request_type' => 'post',
                'value' => $salefilter->name,
                'name' => 'name',
                'label' => 'имя фильтра',
            ]); ?>
        </div>
    </div>
</div>



