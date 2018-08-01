<?php

 use yii\helpers\Html;
 use app\models\User;
 use app\components\Mdb;
 use app\models\SaleFilters;
use app\models\Sale;
/* @var $this yii\web\View
/* @var $model app\models\SaleFiltersSearch2 */


?>

<div class="sale-filters-search">
    <form method="get" id="w0">
        <div class="row">
            <?php   if (Yii::$app->user->can('admin')) { ?>
                <div class="col-sm-8 col-md-6 col-lg-4 col-12">
                    <?php echo Mdb::ActiveSelect($model, 'user_id',[ 0 => 'Любой'] + User::getAvailableUsersAsArray() ) ?>
                </div>
            <?php } ?>

            <div class="col-sm-4 col-md-3 col-lg-2 col-6">
                <?php echo Mdb::ActiveSelect($model, 'rooms_count',[ 0 => 'Любой'] + Sale::ROOMS_COUNT_ARRAY ); ?>
            </div>

            <div class="col-sm-4 col-md-3 col-lg-2 col-6">
                <?php echo Mdb::ActiveSelect($model, 'type',[ 0 => 'Любой'] + SaleFilters::TYPE_OF_FILTERS_ARRAY_PUBLIC ); ?>
            </div>


            <div class="col-sm-6 col-md-6 col-lg-3 col-9">
                <?php echo Mdb::ActiveTextInput($model, 'name',['label' => 'поиск по имени'] ) ?>
            </div>

            <div class="col-sm-2 col-md-2 col-lg-1 col-3">

                <div class="form-group">
                    <?= Html::submitButton(ICON_SEARCH, ['class' => CLASS_BUTTON]) ?>

                </div>
            </div>


        </div>

</div>
</div>
