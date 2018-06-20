<?php

use yii\helpers\Html;


use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SaleListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


?>

<div class="sale-lists-index">
    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'options' => ['class' => 'form-inline'],
        'action' => 'super-filter'
    ]) ?>
    <div role="tabpanel" class="tab-pane" id="savefilter">
        <div class="col-xs-10"> Сохранить фильтр под названием <br>
            <?= $form->field($superfilter, 'name')->textInput(['size' => 50])->label('') ?> <br>

        </div>
        <div class="col-xs-2"> Период <br>
            От <?= $form->field($superfilter, 'date_start')->textInput(['size' => 2])->label('') ?>
            <br>
            До<?= $form->field($superfilter, 'date_finish')->textInput(['size' => 2])->label('') ?>
        </div>
        <div class="col-xs-2"> Скидка (в % ) <br>
            <?= $form->field($superfilter, 'discount')->textInput(['size' => 2])->label('') ?>
        </div>

        <div class="form-group">
            <?= Html::submitButton('Сохранить фильтр', ['class' => 'btn btn-primary', 'formaction' => '/sale/save-current-filter'])
            ?>
        </div>

        <?php if ($update_button) { ?>
            <div class="form-group">
                Фильтр с данным имене существует, заменить?
                <?= Html::submitButton('Да', ['class' => 'btn btn-danger', 'formaction' => '/sale/update-current-filter'])
                ?>
            </div>


            <?php
        }
        ?>


    </div>


</div>

<div class="form-group">
    <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
</div>

</div>


<?php ActiveForm::end(); ?>


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
                <td><?php echo $tr['price']; ?>
                    <br>
                    <?php echo round($avg_price_array[$tr['id']]); ?>
                    <br>
                    <?php echo round($avg_price_address[$tr['id']]); ?>
                </td>
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


                </td>
            </tr>
        <?php endforeach; ?>

        </tbody>
    </table>
</div>


<?php
// display pagination
echo LinkPager::widget([
    'pagination' => $pages,
]);
?>
</div>
