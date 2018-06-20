<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use app\models\SaleFilters;


/* @var $this yii\web\View */
/* @var $model app\models\Sale */
/* @var $form ActiveForm */



?>
<div class="sale-index">







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
                    <th>Url</th>
                </tr>
                </thead>
                <tbody>


                <?php foreach ($data as $tr): ?>
                    <tr>

                        <td>
                            <?php
                            if (!empty($tr['images'])) {
                                $list_of_images = explode("X", $tr['images']);
                                $id = $tr['id'];
                                echo "
								
								
								<a href=\"view?id=" . $id . "\"><img src=" . $list_of_images[1] . " width=\"100\" height=\"100\" > </a>";

                            }

                            ?>

                        </td>

                        <td><?php if ($tr['rooms_count'] == 30) echo "Комн."; else echo $tr['rooms_count'] . "к"; ?></td>

                        <td><?php echo $tr['address']; ?>
                            <br> <?php echo $tr['floor'] . "/" . $tr['floorcount'] . "эт."; ?>
                            <br> <?php echo round(((time() - $tr['date_start']) / 86400), 0) . " дн. назад"; ?>

                        </td>
                        <td><?php echo $tr['description']; ?></td>
                        <td><?php echo $tr['price']; ?></td>
                        <td><?php echo $tr['grossarea']; ?></td>
                        <td>
                            <?php if ($tr['status_blacklist2'] == 1) { ?>
                                <button class='btn btn-info'><?php echo $tr['phone1']; ?></button>
                            <?php } elseif ($tr['status_blacklist2'] == 2) { ?>
                                <button class='btn btn-alert'><?php echo $tr['phone1']; ?></button>
                            <?php } elseif ($tr['status_blacklist2'] == 3) { ?>
                                <button class='btn btn-primary'><?php echo $tr['phone1']; ?></button>
                            <?php } ?>
                        </td>
                        <td><a href="<?php echo $tr['url']; ?>" target="_blank">Link</a>
                            <br>

                            <button class="btn btn-danger btn-xs filter-item-del-button" data-id="<?= $salefilter->id ?>"
                                    data-id_item ="<?= $tr['id'] ?>"><span class="glyphicon glyphicon-minus"></span></button>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>






