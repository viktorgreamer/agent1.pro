<?php

use app\models\SaleFilters;
use app\models\Renders;

?>

<button type="button" class="btn btn-info btn-rounded"
        data-toggle="modal"
        data-target="#prices-lists-modal">Посмотреть все прайслисты
</button>
<div class="modal fade" id="prices-lists-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-info" role="document">
        <!--Content-->
        <div class="modal-content">
            <!--Header-->
            <div class="modal-header">
                <? $salefilters = SaleFilters::find()->where(['type' => SaleFilters::PUBLIC_TYPE])->orderBy('regions')->asArray()->all();
                $grouped_salefilters = array_group_by($salefilters, 'regions');

                ?>

                    <ul class="nav nav-tabs nav-justified" role="tablist">
                        <? $i = 0;
                        foreach ($grouped_salefilters as $key => $lists) {
                            $region = SaleFilters::findOne($key);

                            if ($region) {
                                $i++;
                                ?>
                                <li class="nav-item">
                                    <a class="nav-link text-white <?php if ($i == 1) echo "active"; ?>"
                                       style="padding: 0px" data-toggle="tab" href="#panel<?= $key; ?>"
                                       role="tab"><?= $region->name; ?></a>
                                </li>
                                <?php

                            }
                        }
                        ?>
                    </ul>

                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" class="white-text">×</span>
                </button>
            </div>

            <!--Body-->
            <div class="modal-body">


                <div class="text-left">
                    <div class="tab-content" style="padding-top: 0rem">
                        <? $i = 0;
                        foreach ($grouped_salefilters as $key => $lists) {
                            $region = SaleFilters::findOne($key);
                            if ($region) {
                                $i++; ?>
                                <div class="tab-pane fade in show <?php if ($i == 1) echo "active"; ?>"
                                     id="panel<?= $key; ?>" role="tabpanel">
                                    <br>
                                    <? foreach ($lists as $salefilter2) {
                                        $salefilter1 = SaleFilters::findOne($salefilter2['id']);
                                        if ($salefilter1) { ?>
                                            <div class="row">
                                                <div class="col-12">
                                                    <a href="<?= \yii\helpers\Url::to(['web/search-by', 'id' => $salefilter1->id]); ?>">
                                                        <h5>
                                                            <span class="badge cyan animated pulse "><?php echo $salefilter1->name; ?> </span>
                                                        </h5>
                                                    </a>
                                                    <h6><?php echo $salefilter1->komment; ?></h6>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-9">
                                                    от <span
                                                            class="badge badge-pill light-blue"><?php echo Renders::Price($salefilter1->getMinPrice()); ?></span>
                                                    до <span
                                                            class="badge badge-pill light-blue"><?php echo Renders::Price($salefilter1->getMaxPrice()); ?></span>
                                                </div>
                                                <div class="col-3"><span
                                                            class="badge badge-pill pink"><?php echo $salefilter1->getCount(); ?>
                                                        ШТ.</span>
                                                </div>

                                            </div>
                                            <hr>
                                        <?php }
                                    } ?>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

        </div>
        <!--/.Content-->
    </div>
</div>

