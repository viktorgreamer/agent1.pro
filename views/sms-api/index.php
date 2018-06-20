<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SmsApiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Sms Apis';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sms-api-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Sms Api', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('edit', ['edit','id' => $sms_api->id_list], ['class' => 'btn btn-success']) ?>
    </p>
    <?php






    ?>
    <div class="sale-lists-index">

        <h1><?= Html::encode($this->title) ?></h1>

        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'options' => ['class' => 'form-inline'],
            'action' => 'index'
        ]) ?>


        <div class="row">
            <div class="col-xs-4">Название списка


                <?php // выбор периода подачит обьявлений
                echo \app\components\MdbSelect::widget([
                    'request_type' => 'get',
                    'name' => 'id_list',
                    'value' => $sms_api->id_list,
                    'placeholder' => 'список',
                    'options' => \app\models\SmsApi::getMyLists(),
                    'label' => '',
                    'color' => 'primary'
                ]);

?>
                <div class="form-group">
                    <?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>
                </div>

                <?php ActiveForm::end(); ?>


                <?
                // var_dump($salelist->list_of_ids); ?>


                <div class="container">
                    <table class='table table-striped'>
                        <thead>
                        <tr>
                            <th># комнат</th>
                            <th>S</th>
                            <th>текст</th>
                            <th>Адрес</th>
                             <th>Цена</th>
                            <th>имя</th>
                            <th>Телефон</th>
                        </tr>
                        </thead>
                        <tbody>


                        <?php if (count($data) != 0) {
                            foreach ($data as $tr): ?>
                                <tr>
                                    <td><?php echo $tr['rooms_count']; ?>

                                    </td>
                                    <td><?php echo $tr['grossarea']; ?>
                                    </td>


                                    <td><?php echo $tr['text_sms']; ?>

                                    </td>
                                    <td><?php echo $tr['address']; ?>
                                    </td>


                                    <td><?php echo $tr['price']; ?></td>

                                    <td><?php echo $tr['person']; ?></td>
                                    <td><?php echo $tr['phone']; ?></td>
                                    <td>


                                        <button class="btn btn-danger btn-xs sms-api-del-button"
                                                data-id="<?= $tr['id'] ?>"
                                                data-id_list="<?= $sms_api->id_list ?>"><span
                                                class="fa fa-minus"></span></button>


                                    </td>
                                </tr>
                            <?php endforeach;
                        } else "НЕТ ДАННЫХ";
                        ?>

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
