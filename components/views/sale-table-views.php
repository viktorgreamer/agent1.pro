<?php
echo count($sales);
?>

<div class="container">

    <table class='table table-striped'>
        <thead>
        <tr>
            <th>картинка</th>
            <th>Ком</th>
            <th>Адрес</th>
            <th>Описание</th>
            <th>Цена</th>
            <th>Пл.</th>
            <th>Телефон</th>
            <th>url</th>
        </tr>
        </thead>
        <tbody>

        <? foreach ($sales as $sale): ?>
            <tr>

                <td>
                    <?php
                    if (!empty($sale->images)) {
                        $list_of_images = explode("X", $sale->images);
                        $id = $sale->id;
                        echo "
								
								
								<a href=\"view?id=" . $id . "\"><img src=" . $list_of_images[1] . " width=\"100\" height=\"100\" > </a>";

                    }

                    ?>

                </td>

                <td><?php if ($sale->rooms_count == 30) echo "Комн."; else echo $sale->rooms_count . "к"; ?></td>

                <td><?php echo $sale->address; ?>
                    <br> <?php echo $sale->floor . "/" . $sale->floorcount . "эт."; ?>
                    <br> <?php echo round(((time() - $sale->date_start) / 86400), 0) . " дн. назад обновлено"; ?>
                    <br> <?php echo round(((time() - $sale->original_date) / 86400), 0) . " дн. назад подано"; ?>
                    <br> <?php echo  round($sale->count_of_views / (time() - $sale->original_date) * 60 * 60 * 24)." просмотров в сутки "; ?>


                </td>
                <td><?php echo $sale->description; ?></td>
                <td><?php echo $sale->price; ?></td>
                <td><?php echo $sale->grossarea; ?></td>
                <td>
                    <?php if ($sale->status_blacklist2 == 1) { ?>
                        <button class='btn btn-info'><?php echo $sale->phone1; ?></button>
                    <?php } elseif ($sale->status_blacklist2 == 2) { ?>
                        <button class='btn btn-alert'><?php echo $sale->phone1; ?></button>
                    <?php } elseif ($sale->status_blacklist2 == 3) { ?>
                        <button class='btn btn-primary'><?php echo $sale->phone1; ?></button>
                    <?php } else { ?>
                        <button class='btn btn-danger'><?php echo $sale->phone1; ?></button>
                    <? } ?>
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
        <?php endforeach; ?>


        </tbody>
    </table>
</div>