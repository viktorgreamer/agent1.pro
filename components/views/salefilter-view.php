
<?php



if (!empty($salefilter->rooms_count)) $body = " Куплю " . \app\models\RenderSalefilters::RoomsCount($salefilter->rooms_count);

if ($salefilter->price_up) $body .= ", цена до <b>" . $salefilter->price_up . " </b>";
if ($salefilter->grossarea_down) $body .= ", площадью от <b>" . $salefilter->grossarea_down . " </b> м2";
if ($salefilter->year_down) $body .= ",дом нестарше <b>" . $salefilter->year_down . " </b> г.п.";

$body .="<br>".$salefilter->komment;

?>






<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?php echo $salefilter->name; ?></h3>
    </div>
    <div class="panel-body">
        <?php
        echo $body;
        ?>
        <br>
        <button id="#AddTags" type="button" class="btn btn-primary btn-xs"
                data-toggle="modal"
                data-target="#TagsModal"> +tags
        </button>
        ";


        <div class="modal fade" id="TagsModal" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-body">

                        <? echo $this->render('//tags/quick-add-form-salefilter', [
                            'id' => $salefilter->id,
                        ]);

                        ?>




                    </div>
                </div>
            </div>
        </div>



    </div>
</div>