<?php
namespace app\components;
use yii\helpers\Html;
use app\models\Sale;

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
                    <br> <?php echo $sale->year; ?>
                    <br> <?php echo $sale->floor . "/" . $sale->floorcount . "эт."; ?>
                    <br> <?php echo round(((time() - $sale->date_start) / 86400), 0) . " дн. назад"; ?>

                </td>
                <td><?php echo $sale->description; ?></td>
                <td><?php echo $sale->price; ?>
                    <br>
<!--
                <?php /*echo $sale->average_price; */?> ( <?/* echo $sale->average_price_count; */?> )
                <br>
              <?php /*echo $sale->average_price_address; */?> (<?/*= Html::a($sale->average_price_address_count,  ['sale-analitics/show-same-address-objects', 'id' => $sale->id]); */?> )
<br>-->

                    <?php echo $sale->average_price_same; ?> ( <?= Html::a($sale->average_price_same_count,  ['sale-analitics/show-nearest-same-objects', 'id' => $sale->id]); ?> )
              <!--  <?php /*if ($sale->grossarea != 0) echo round($sale->price/$sale->grossarea); */?> ( <?/* echo $sale->average_price_m2(); */?> )-->
                </td>
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

                    <!-- здесь выводятся кнопки упраления добавления или удаления в опредлеленные списки-->
                    <?php if (!in_array($sale->id, explode(",", $salefilter->black_list_id))) {
                        // если  не находится в списке banned ?>
                        <button class="btn btn-danger btn-xs filter-item-del-button"
                                data-id="<?= $salefilter->id ?>"
                                data-id_item="<?= $sale->id ?>"><span class="glyphicon glyphicon-ban-circle"></span>
                        </button>
                    <? }  else {
                        // если находится в списке banned ?>

                        <button class="btn btn-danger btn-xs filter-delete-item-from-black-list-button"
                                data-id="<?= $salefilter->id ?>"
                                data-id_item="<?= $sale->id ?>"><span class="glyphicon glyphicon-ban-circle"></span>
                        </button>
                    <?php } ?>

                    <?php if (!in_array($sale->id, explode(",", $salefilter->white_list_id))) {  ?>
                        <button class="btn btn-xs filter-item-add-button"
                                data-id="<?= $salefilter->id ?>"
                                data-id_item="<?= $sale->id ?>"><span class="glyphicon glyphicon-star-empty"></span>
                        </button>
                    <? }  else { ?>
                        <button class="btn btn-warning btn-xs filter-delete-item-from-white-list-button"
                                data-id="<?= $salefilter->id ?>"
                                data-id_item="<?= $sale->id ?>"><span class="glyphicon glyphicon-star"></span>
                        </button>
                    <?php } ?>

                    <?= Html::a('Анализ', ['sale-analitics/show', 'id' => $sale->id]); ?>


                </td>
            </tr>
        <?php endforeach; ?>


        </tbody>
    </table>
</div>