<?php

?>

<div class="container">

    <table class='table table-striped'>
        <thead>
        <tr>
            <th>картинка</th>
            <th>Ком</th>
            <th>Адрес</th>
   <th>Цена</th>
            <th>Телефон</th>
            <th>url</th>
        </tr>
        </thead>
        <tbody>

        <? if (count($sales) > 0) {


            foreach ($sales as $sale): ?>
                <tr>

                    <td>
                        <?php
                        if (!empty($sale->images)) {
                            $list_of_images = explode("X", $sale->images);
                            $id = $sale->id;
                            //  echo "<a href=\"view?id=" . $id . "\"><img src=" . $list_of_images[1] . " width=\"100\" height=\"100\" > </a>";

                        }

                        ?>

                    </td>

                    <td><?php if ($sale->rooms_count == 30) echo "Комн."; elseif ($sale->rooms_count == 20) echo "Студия";
                        else  echo $sale->rooms_count . "к"; ?></td>

                    <td><?php echo $sale->address; ?>
                        <br> <?php echo " id_address" . $sale->id_address; ?>
                        <br> <?php echo $sale->floor . "/" . $sale->floorcount . "эт."; ?>
                        <br> <?php echo $sale->grossarea . "/" . $sale->living_area . "/" . $sale->kitchen_area . "м"; ?>
                        <br> <?php echo round(((time() - $sale->date_start) / 86400), 0) . " дн. назад"; ?>

                    </td>

                    <td><?php echo $sale->price; ?></td>
                    <!--                    <td>--><?php //echo $sale->grossarea; ?><!--</td>-->
                    <td>
                        <?php if ($sale->status_blacklist2 == 1) { ?>
                            <button class='btn btn-alert'><?php echo $sale->phone1; ?></button>
                        <?php } elseif ($sale->status_blacklist2 == 0) { ?>
                            <button class='btn btn-primary'><?php echo $sale->phone1; ?></button>
                        <?php } ?>
                    </td>
                    <td><a href="<?php echo $sale->url; ?>" target="_blank">Link</a>
                        <br>
                        <?php if ($salefilter) {
                            ?>


                            <button class="btn btn-danger btn-xs filter-item-del-button"
                                    data-id="<?= $salefilter->id ?>"
                                    data-id_item="<?= $sale->id ?>"><span class="glyphicon glyphicon-minus"></span>
                            </button>
                            <?php
                        }
                        ?>

                    </td>
                </tr>
            <?php endforeach;
        } ?>


        </tbody>
    </table>
</div>