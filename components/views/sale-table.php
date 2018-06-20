<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\helpers\Url;


?>

    <div class="container-fluid">

        <?php echo " всего " . $totalcount . " вариантов"; ?>

        <? if (count($sales) > 0) {
            foreach ($sales as $sale):
                $SaleWidget = New \app\components\SaleWidget();
                $SaleWidget->load($sale);
                $address = \app\models\Addresses::findOne($sale->id_address); ?>

                <div class="row" id="row_<?= $sale->id; ?>">
                    <div class="col-sm-1"><?= $SaleWidget->url; ?>
                        <div class="row" style='min-height: 90px;padding-right: 20px;'>
                            <? echo $SaleWidget->photos; ?>
                        </div>
                    </div>
                    <?php $similarsales = \app\models\Sale::find()
                        ->where(['in', 'id', explode(",", $sale->similar_ids)])
                        ->all();

                    if ($similarsales) {
                        $body = '';
                        $body_tags = '';
                        foreach ($similarsales as $item) {
                            $SaleWidgetsMini = new \app\components\SaleWidget();
                            $SaleWidgetsMini->Load($item);

                            $body .= $SaleWidgetsMini->title . " " . $SaleWidgetsMini->url . " " . $SaleWidgetsMini->phone . "<br>";
                            if ($item->moderated != 0) $body_tags .= $SaleWidgetsMini->title . "<button class='btn btn-success copytags' data-id_from=$item->id data-id_to=$sale->id>Copy</button> <br>";

                        }
                    }
                    ?>

                    <div class="col-sm-2">
                        <strong> <?php echo $SaleWidget->rooms_count; ?></strong>
                        <br>

                        <?php echo $SaleWidget->address; ?>
                        <? if ($similarsales) {
                            ?>
                            <a tabindex="0" data-toggle="popover" data-placement="top" title="Похожие варианты"
                               data-trigger="focus" data-html="true"
                               data-content="<?= $body ?>"><span
                                        class="badge badge-pill light-blue"><?= count($similarsales) ?></span></a>
                        <? } ?>
                        <small>
                            <br> <?php echo $SaleWidget->floors;
                            echo $SaleWidget->house_type; ?>
                            <?php echo $SaleWidget->areas; ?>
                            <br> <?php echo $SaleWidget->days_ago; ?>
                        </small>
                        <a id="#AddTagsAddress<?php echo $SaleWidget->id_address; ?>" type="button" href="#"
                           data-toggle="modal"
                           data-target="#TagsModal<?= $SaleWidget->id_address ?>"> <i
                                    class="fa fa-tags green-text fa-2x" aria-hidden="true"></i>
                        </a>
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
                        <a id="#AddmanualAddress<?php echo $SaleWidget->id; ?>" type="button" href="#"
                           data-toggle="modal"
                           data-target="#manualAddress<?= $SaleWidget->id; ?>">manual
                        </a>
                        <div class="modal fade" id="manualAddress<?php echo $SaleWidget->id; ?>" tabindex="-1"
                             role="dialog"
                             aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">

                                        <?= Html::input('text', "input_text_" . $SaleWidget->id, '', ['class' => 'form-control', 'id' => "input_text_" . $SaleWidget->id]) ?>
                                        <?= Html::a('Поиск', '#', ['class' => 'btn btn-success', 'id' => "searching_button" . $SaleWidget->id]); ?>

                                        <?php
                                        $url = Url::toRoute("addresses/quick-search-pjax");
                                        $script = <<< JS
$('#searching_button$SaleWidget->id').on('click', function() {
text = $('#input_text_$SaleWidget->id').val();
console.log(text);
$('#searchingstreet$SaleWidget->id').load(encodeURI('$url?id=$SaleWidget->id_address&address='+text));
});
JS;
                                        $this->registerJs($script, yii\web\View::POS_READY);
                                        ?>
                                        <div id="searchingstreet<?php echo $SaleWidget->id; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <? // echo Html::a('manual', ['addresses/quick-search', 'id' => $SaleWidget->id], ['target' => '_blank'])
                        ?>


                    </div>
                    <div class="col-sm-4">
                        <small>  <?php echo mb_strimwidth($sale->description, 0, 500, '...'); ?></small>
                        <br>
                        <!-- блок модального окна добавления tags  -->

                        <a id="#AddTags<?= $SaleWidget->id ?>" type="button" href="#"
                           data-toggle="modal"
                           data-target="#TagsModal<?= $SaleWidget->id ?>"> <i class="fa fa-tags blue-text fa-2x"
                                                                              aria-hidden="true"></i>
                        </a>


                        <div class="modal fade" id="TagsModal<?= $SaleWidget->id ?>" tabindex="-1" role="dialog"
                             aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                    <div class="modal-body">

                                        <? echo $this->render('//tags/quick-add-form', [
                                            'id' => $SaleWidget->id,
                                        ]);
                                        //  echo $body_tags;

                                        ?>


                                    </div>
                                </div>
                            </div>
                        </div>
                        <a type="button" class="set-moderated" href="#" data-id="<?= $SaleWidget->id ?>"> <i
                                    class="fa fa-check green-text fa-2x" aria-hidden="true"></i>
                        </a>
                        <?php echo $SaleWidget->tags_button; ?>
                        <?php
                        $this->registerJs($SaleWidget->TagRegisterJs, yii\web\View::POS_READY);
                        ?>

                        <?php
                        // my_var_dump(array_merge($address->getTags() ,$sale->tags));
                        if ($address) {
                            $tags = array_merge($address->getTags(), $sale->tags);
                        } else $tags = $sale->tags;
                      //  echo \app\components\TagsWidgets::widget(['sale_id' => $tags]);
                        ?>


                    </div>

                    <div class="col-sm-4">
                        <div class="row">
                            <div class="col-sm-5">
                                <small>
                                    <?php
                                    echo $SaleWidget->price;
                                    // если устаовлен флажек показывать статистику

                                    if (in_array('show_stat', explode(",", $options))) {
                                        echo "<br>" . $SaleWidget->sale_stat;
                                    }
                                    ?>
                                </small>
                            </div>
                            <div class="col-sm-7">
                                <?php if ($sale->status_blacklist2 == 1) { ?>
                                    <h5><i class="fa fa-user-secret" aria-hidden="true"></i><span
                                                class="badge badge-info">( <?php // echo $SaleWidget->count_of_ads; ?>
                                            )</span>
                                        <span class="badge badge-danger">
                                            <?php echo $sale->person . "<br>"; ?><?php echo $SaleWidget->phone;
                                            ?></span></h5>
                                    <div class="row justify-content-end">

                                    </div>

                                    <?php if ($SaleWidget->phone2) { ?>
                                        <h5><?php echo $SaleWidget->phone2; ?></h5>
                                    <? } ?>
                                <?php } elseif ($sale->status_blacklist2 == 0) { ?>
                                    <h5><i class="fa fa-user-secret"
                                           aria-hidden="true"></i> <?php echo $sale->person; ?><span
                                                class="badge primary-color"><?php echo $SaleWidget->phone;
                                            ?></span></h5>

                                <?php } ?>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-1">

                        <?php if ($controls) {
                            ?>
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger dropdown-toggle px-3" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-icons">
                                    <a class="dropdown-item error-geocodetion" data-id_item="<?= $sale->id ?>" href="#">
                                        Ошибка в адресе</a>
                                    <a class="dropdown-item set-sold" data-id_item="<?= $sale->id ?>"
                                       href="#">Продано</a>
                                </div>
                            </div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-success dropdown-toggle px-3"
                                        data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-icons">

                                    <a class="dropdown-item" onclick="copyToClipboard('#id<?= $sale->id; ?>')"><i
                                                class="fa fa-clipboard fa-2x"
                                                aria-hidden="true"></i> Copy</a>

                                    <a href="#" class="dropdown-item add-object-to-favourites"
                                       data-id_item="<?= $sale->id ?>"> <i
                                                class="fa fa-plus fa-2x"
                                                aria-hidden="true"></i> Add</a>

                                </div>
                            </div>
                            <!-- здесь выводятся кнопки упраления добавления или удаления в опредлеленные списки-->
                            <div class="btn-group">
                                <button type="button" class="btn btn-indigo dropdown-toggle px-3" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu dropdown-icons">

                                    <?php if ($salefilter->id != 0) { ?>

                                        <? if (!in_array($sale->id, explode(",", $salefilter->black_list_id))) {
                                            // если  не находится в списке banned ?>
                                            <a class="dropdown-item filter-item-del-button"
                                               data-id="<?= $salefilter->id ?>"
                                               data-id_item="<?= $sale->id ?>"><i class="fa fa-ban fa-2x"
                                                                                  aria-hidden="true"></i>
                                            </a>
                                        <? } else {
                                            // если находится в списке banned ?>

                                            <a class="dropdown-item filter-delete-item-from-black-list-button"
                                               data-id="<?= $salefilter->id ?>"
                                               data-id_item="<?= $sale->id ?>"><i class="fa fa-ban fa-2x"
                                                                                  aria-hidden="true"></i>
                                            </a>
                                        <?php } ?>

                                        <?php if (!in_array($sale->id, explode(",", $salefilter->white_list_id))) { ?>
                                            <a class="dropdown-item filter-item-add-button"
                                               data-id="<?= $salefilter->id ?>"
                                               data-id_item="<?= $sale->id ?>"><i class="fa fa-star-o fa-2x"
                                                                                  aria-hidden="true"></i>
                                            </a>
                                        <? } else { ?>
                                            <a class="dropdown-item filter-delete-item-from-white-list-button"
                                               data-id="<?= $salefilter->id ?>"
                                               data-id_item="<?= $sale->id ?>"><i class="fa fa-star fa-2x"
                                                                                  aria-hidden="true"></i>
                                            </a>
                                        <?php } ?>
                                        <a class="dropdown-item filter-address-del-button"
                                           data-id="<?= $salefilter->id ?>"
                                           data-id_address="<?= $sale->id_address ?>"><i class="fa fa-remove fa-2x"
                                                                                         aria-hidden="true">Удалить
                                                Адрес</i>
                                        </a><a class="dropdown-item filter-item-check-button"
                                               data-id="<?= $salefilter->id ?>"
                                               data-id_item="<?= $sale->id ?>"><i class="fa fa-exclamation fa-2x"
                                                                                  aria-hidden="true"></i>Контроль</i>
                                        </a>
                                    <? } ?>
                                    <?php if ($salelist->id != 0) { ?>
                                        <a class="dropdown-item salelist-del-button"
                                           data-id="<?= $salelist->id ?>"
                                           data-id_item="<?= $sale->id ?>"><i
                                                    class="fa fa-minus-circle red-text fa-2x"
                                                    aria-hidden="true"></i></a>
                                        <a class="dropdown-item salelist-ok-button"
                                           data-id="<?= $salelist->id ?>"
                                           data-id_item="<?= $sale->id ?>"><i class="fa fa-check fa-2x"
                                                                              aria-hidden="true"></i></a>
                                        <a class="dropdown-item salelist-ban-button"
                                           data-id="<?= $salelist->id ?>"
                                           data-id_item="<?= $sale->id ?>"><i class="fa fa-ban fa-2x"
                                                                              aria-hidden="true"></i></a>
                                        <a class="dropdown-item list-address-del-button"
                                           data-id="<?= $salelist->id ?>"
                                           data-id_address="<?= $sale->id_address ?>"><i class="fa fa-remove fa-2x"
                                                                                         aria-hidden="true">Удалить
                                                Адрес</i>
                                        </a>
                                    <?php } ?>

                                </div>
                            </div>
                            <?php
                        }
                        ?>

                        <p id="id<?= $sale->id; ?>" hidden><?php echo $SaleWidget->title_to_copy; ?> </p>


                    </div>
                </div>
                <hr>

            <?php endforeach;
        } ?>


    </div>
<?php





