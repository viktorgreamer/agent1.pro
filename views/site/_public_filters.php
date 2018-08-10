<?php
$salefilters = \app\models\SaleFilters::find()->where(['type' => \app\models\SaleFilters::PUBLIC_TYPE])->andWhere(['status' => \app\models\SaleFilters::STATUS_ACTIVE])->all();

?>
<h2 class="h1-responsive font-weight-bold text-center my-5">Готовые прайс-листы</h2>
<p class="text-center h3-responsive w-responsive mx-auto mb-5">
    Можно пользоваться готовыми прайс-листами</p>
<div class="container">
    <div class="row">
        <?php foreach ($salefilters as $salefilter) { ?>
            <div class="col-lg-4">
                <?php echo \app\components\SaleFilterWidgets::widget(
                    [
                        'salefilter' => $salefilter,
                        'type' => 'web'
                    ]); ?>

            </div>
        <? } ?>
    </div>
</div>
