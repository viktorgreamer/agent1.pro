<?php
use yii\helpers\Html;
use yii\bootstrap\Collapse;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use app\components\TagsWidgets;

$sale = $model;
?>

    <div class="container-fluid">

        <?php $SaleWidget = New \app\components\SaleWidget();
                $SaleWidget->load($sale);

                ?>

                <div class="row">
                    <div class="col-lg-3 col-md-3 col-sm-4" style="margin-bottom: 5px">
                        <div class="row" style='min-height: 90px;padding-left: 20px;'>

                            <? echo $SaleWidget->photos; ?>


                        </div>
                    </div>


                    <div class="col-lg-6 col-md-6 col-sm-8">

                        <h6><?php echo $SaleWidget->rooms_count; ?><?php echo $SaleWidget->address; ?>
                            <br><strong>   <?php echo $SaleWidget->floors;
                                echo $SaleWidget->house_type; ?>
                                <?php echo $SaleWidget->areas; ?></strong>
                        </h6>
                        <?php
                        echo TagsWidgets::widget(['tags' => $sale->tags]);
                        ?>

                    </div>


                    <div class="col-sm-12 col-lg-3 col-md-3 ">
                        <div class="row justify-content-end">

                            <?php if (((!empty($options)) and (in_array('show_date_of_die', $options)))) {
                                echo "<h3> <span class=\"badge red\"><strike>" . $SaleWidget->price . "</strike></span></h3>";
                                echo "<br><h4><span class='badge badge-danger animated flash'>Продано <br> " . date("d.m.y H:i:s", $sale->date_of_die) . "</span></strike></h4>";
                            } else {
                            echo "<h5> <span class=\"badge red\">" . $SaleWidget->price . "</span></h5>"; ?>
                        </div>
                        <div class="row justify-content-end">
                            <?php echo "<h6>8-921-730-40-30</h6>";
                            } ?>

                        </div>

                    </div>


                </div>
                <hr>




    </div>
<?php




