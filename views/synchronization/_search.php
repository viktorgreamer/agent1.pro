<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\components\Mdb;

/* @var $this yii\web\View */
/* @var $model app\models\SynchronizationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

    <div class="synchronization-search">

        <form action="index" method="get" id="w0">
            <div class="row">

                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'id_sources', [0 => 'любой'] + \app\models\Sale::ID_SOURCES) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'rooms_count', [0 => 'любое'] + \app\models\Sale::ROOMS_COUNT_ARRAY) ?>
                        </div>
                    </div>
                </div>


                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'sync', [0 => 'любой'] + \app\models\Sale::TYPE_OF_SYNC) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'parsed', [0 => 'любой'] + \app\models\Sale::TYPE_OF_PARSED) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'geocodated', [0 => 'любой'] + \app\models\Geocodetion::GEOCODATED_STATUS_ARRAY) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'processed', [0 => 'любой'] + \app\models\Sale::TYPE_OF_PROCCESSED) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'load_analized', [0 => 'любой'] + \app\models\Sale::TYPE_OF_ANALIZED) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'disactive', [10 => 'любой'] + \app\models\Sale::DISACTIVE_CONDITIONS_ARRAY) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'status', [0 => 'любой'] + \app\models\Sale::STATUSES_ARRAY) ?>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'moderated', [0 => 'любой'] + \app\models\Sale::TYPE_OF_MODERATED) ?>

                        </div>
                    </div>

                </div>

                <div class="col-sm-1">
                    <div class="row">
                        <? echo "<input type=\"text\" class=\"form-control\" name=\"id\" placeholder=\"id\" value='" . Yii::$app->request->get('id') . "'>"; ?>
                        <? echo "<input type=\"text\" class=\"form-control\" name=\"id_in_source\" placeholder=\"id_in_source\" value='" . Yii::$app->request->get('id_in_source') . "'>"; ?>
                    </div>
                </div>


                <div class="col-sm-1">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-6">
                                <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i>', ['class' => 'btn btn-success btn-sm']) ?>
                            </div>

                        </div>


                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-sm-2">
                    <div class="row">

                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_check_down', ['label' => 'Время проверки от']) ?>
                        </div>
                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_check_up', ['label' => 'Время проверки до']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">

                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_die_down', ['label' => 'Время удаления от']) ?>
                        </div>
                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_die_up', ['label' => 'Время удаления до']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">

                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_start_down', ['label' => 'Время появления от']) ?>
                        </div>
                        <div class="col-12">
                            <?= Mdb::ActiveDatepicker($model, 'date_of_start_up', ['label' => 'Время появления до']) ?>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2">
                    <div class="row">
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'sort_by',\app\models\SaleFilters::mapSorting() ) ?>

                        </div>
                        <div class="col-12">
                            <?= Mdb::ActiveSelect($model, 'log',[ 0 => "ANY"] + \app\models\SaleLog::mapTypes()) ?>

                        </div>
                    </div>
                </div>
            </div>


        </form>

    </div>
<?php

$script = <<< JS
$('.datepicker').pickadate();
JS;
//маркер конца строки, обязательно сразу, без пробелов и табуляции
$this->registerJs($script, yii\web\View::POS_READY);

