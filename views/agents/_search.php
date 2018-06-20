<?php
use \app\components\Mdb;
use yii\helpers\Html;
?>

<div class="agents-search">

    <form action="index" method="get" id="w0">
        <div class="row">

            <div class="col-sm-2">
                <div class="row">
                    <div class="col-12">
                        <?= Mdb::TextInput( 'phone', ['label' => 'Телефон']) ?>
                    </div>
                </div>

            </div>
            <div class="col-sm-2">
                <div class="row">
                    <div class="col-12">
                        <?= Mdb::Select( 'status', 'Статус',[ 0 => 'Все равно',  1 => 'NOT MODERATED', 2 => 'MODERATED']); ?>
                    </div>
                </div>

            </div>
            <div class="col-sm-2">
                <div class="row">
                    <div class="col-12">
                        <?= Mdb::Select( 'person_type', 'Статус',[ 10 => 'Все равно',  0 => 'ХОЗЯИН', 1 => 'АГЕНТ']); ?>
                    </div>
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


    </form>

</div>
