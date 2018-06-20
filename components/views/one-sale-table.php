<?php
echo $sale->images;
$SaleWidget = New \app\components\SaleWidget();
$SaleWidget->load($sale);
?>

<div class="row">
    <div class="col-sm-2">
        <div class="row">
            <div class="col-sm-8">
                1212
                <? echo $SaleWidget->photos;
                ?>


            </div>
            <div class="col-sm-4">
                <?php echo $SaleWidget->rooms_count; ?>
                <a href="<?php echo $sale->url; ?>"
                   target="_blank"><?php echo render_id_resourse($sale->id_sources) ?></a>
            </div>
        </div>
    </div>

    <div class="col-sm-2">
        <?php echo $SaleWidget->address; ?>
        <span class="fa-stack error-geocodetion" data-id_item="<?= $sale->id ?>">
  <i class="fa fa-map-marker fa-stack-1x"></i>
  <i class="fa fa-ban fa-stack-2x text-danger"></i>
</span>
        <span class="fa-stack set-sold" data-id_item="<?= $sale->id ?>">
<i class="fa fa-trash" aria-hidden="true"></i>

</span>
        <br> <?php echo $SaleWidget->floors;
        echo $SaleWidget->house_type; ?>
        <?php echo $SaleWidget->areas; ?>
        <br> <?php echo $SaleWidget->days_ago; ?>
        <button id="#AddTagsAddress<?php echo $SaleWidget->id_address; ?>" type="button"
                class="btn btn-success btn-xs"
                data-toggle="modal"
                data-target="#TagsModal<?php echo $SaleWidget->id_address; ?>"> +tags
        </button>
        ";


        <div class="modal fade" id="TagsModal<?php echo $SaleWidget->id_address; ?>" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <? echo $this->render('//tags/quick-add-form-address', [
                            'id' => $SaleWidget->id_address,
                        ]);

                        ?>


                    </div>
                </div>
            </div>
        </div>
        <button id="#setIdAddress<?php echo $SaleWidget->id; ?>" type="button"
                class="btn btn-success btn-xs"
                data-toggle="modal"
                data-target="#setIdAddressModal<?php echo $SaleWidget->id; ?>"> manual
        </button>
        ";


        <div class="modal fade" id="setIdAddressModal<?php echo $SaleWidget->id; ?>" tabindex="-1"
             role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <? echo $this->render('//addresses/quick-search', [
                            'id' => $SaleWidget->id,
                        ]);

                        ?>


                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-4">
        <?php echo mb_strimwidth($sale->description, 0, 100, '...'); ?>
        <br>
        <!-- блок модального окна добавления tags  -->

        <button id="#AddTags<?= $SaleWidget->id ?>" type="button" class="btn btn-primary btn-xs"
                data-toggle="modal"
                data-target="#TagsModal<?= $SaleWidget->id ?>"> +tags
        </button>
        ";


        <div class="modal fade" id="TagsModal<?= $SaleWidget->id ?>" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <? echo $this->render('//tags/quick-add-form', [
                            'id' => $SaleWidget->id,
                        ]);

                        ?>


                    </div>
                </div>
            </div>
        </div>
        ";
        <?php echo $SaleWidget->tags_button; ?>
        <?php
        $this->registerJs($SaleWidget->TagRegisterJs, yii\web\View::POS_READY);
        ?>
        <br>
        <?php
        echo $SaleWidget->tags;
        ?>


    </div>

    <div class="col-sm-3">
        <div class="row">
            <div class="col-sm-6">
                <?php
                echo $SaleWidget->price;
                // если устаовлен флажек показывать статистику
                if (in_array('show_stat', explode(",", $options))) {
                    echo "<br>" . $SaleWidget->sale_stat;
                }


                ?>
            </div>
            <div class="col-sm-6">
                <?php if ($sale->status_blacklist2 == 1) { ?>
                    <button class='btn btn-alert'><?php echo $SaleWidget->phone;
                        ?></button>
                <?php } elseif ($sale->status_blacklist2 == 0) { ?>
                    <button class='btn btn-primary'><?php echo $SaleWidget->phone;
                        ?></button>
                <?php } ?>
            </div>
        </div>
    </div>


    <div class="col-sm-1">

        <?php if ($controls) {
            ?>
            <!-- здесь выводятся кнопки упраления добавления или удаления в опредлеленные списки-->
            <?php
            if ($salefilter->id != 0) {
                if (!in_array($sale->id, explode(",", $salefilter->black_list_id))) {
                    // если  не находится в списке banned ?>
                    <button class="btn btn-danger btn-xs filter-item-del-button"
                            data-id="<?= $salefilter->id ?>"
                            data-id_item="<?= $sale->id ?>"><i class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                <? } else {
                    // если находится в списке banned ?>

                    <button class="btn btn-danger btn-xs filter-delete-item-from-black-list-button"
                            data-id="<?= $salefilter->id ?>"
                            data-id_item="<?= $sale->id ?>"><i class="fa fa-ban" aria-hidden="true"></i>
                    </button>
                <?php } ?>

                <?php if (!in_array($sale->id, explode(",", $salefilter->white_list_id))) { ?>
                    <button class="btn btn-xs filter-item-add-button"
                            data-id="<?= $salefilter->id ?>"
                            data-id_item="<?= $sale->id ?>"><i class="fa fa-star-o"
                                                               aria-hidden="true"></i>
                    </button>
                <? } else { ?>
                    <button class="btn btn-warning btn-xs filter-delete-item-from-white-list-button"
                            data-id="<?= $salefilter->id ?>"
                            data-id_item="<?= $sale->id ?>"><i class="fa fa-star"
                                                               aria-hidden="true"></i>
                    </button>
                <?php }
            } ?>
            <?php
            if ($salelist->id != 0) {
                ?>
                <button class="btn btn-danger btn-xs salelist-del-button" data-id="<?= $salelist->id ?>"
                        data-id_item="<?= $sale->id ?>"><i class="fa fa-minus-circle"
                                                           aria-hidden="true"></i></button>
            <?php } ?>
            <?php
        }
        ?>
        <p id="id<?= $sale->id; ?>" hidden><?php echo $SaleWidget->title_to_copy; ?> </p>
        <a onclick="copyToClipboard('#id<?= $sale->id; ?>')"><i class="fa fa-clipboard fa-2x"
                                                                aria-hidden="true"></i></a>

        <i class="fa fa-plus add-object-to-favourites"
           data-id_item="<?= $sale->id ?>" aria-hidden="true"></i>

    </div>
</div>
<hr>