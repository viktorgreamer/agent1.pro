<?php

use yii\helpers\Url;
use app\models\SaleLists;
use yii\widgets\ListView;
use app\components\Mdb;

?>

<? if ($salefilter) { ?>
<div class="container">
    <div class="row">
        <div class="col-md-12 col-lg-7 col-xl-7 z-depth-2 my-3" style="background-color: whitesmoke; ">
            <br>
            <div class="container">

                <h3>
                    <strong><?php echo $salefilter->name; ?></strong><br>
                    <small class="text-muted"><?php echo "Описание: " . $salefilter->komment; ?></small>
                </h3>
                <?= Mdb::Badge($salefilter->getCount(), ['class' => "pill light-blue"]); ?> предложений
                от <?= Mdb::Badge(number_format($salefilter->getMinPrice(), 0, ".", "."), ['class' => "pill light-blue"]); ?>
                до <?= Mdb::Badge(number_format($salefilter->getMaxPrice(), 0, ".", "."), ['class' => "pill light-blue"]); ?>
                Руб.
                <div class="row justify-content-end" style="padding-right:15px;">
                    <p class="h6 text-info">
                        Обновлено <?= Yii::$app->formatter->asRelativeTime($salefilter->time); ?></p>
                </div>
                <div class="sticky">

                    <?= $this->render('_lp/_map_modal_view', compact('dataProvider')); ?>

                    <?= $this->render('_lp/_price_lists_modal', compact('dataProvider'));; ?>
                </div>
                <?= $this->render('_list_view', compact('dataProvider'));; ?>
            </div>
        </div>

        <? } ?>


        <!--Grid column-->
        <div class="col-md-12 col-lg-5 col-xl-5 mb-3">
            <?php // показываем похожие прайс листы
            echo $this->render('_lp\_list_relevanted_ids', compact('salefilter'));
            ?>

        </div>
    </div>
    <!--Grid row-->

</div>


<?php echo $this->render('_lp/_about_region', compact('salefilter')); // окно о данном микрорайоне ?>


<?php echo $this->render('_contact_form_modal'); // окно заявки ?>
<?php // echo $this->render('_15sec_promo_modal'); // окно 15 секундого первого предложения ?>

<?= $this->render('_counters'); // счетчики метрики  ?>




