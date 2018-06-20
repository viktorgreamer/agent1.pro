<?php

use yii\helpers\Html;
use yii\widgets\LinkPager;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use app\models\SmsApi;

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

        <?= Html::a('send', ["send?id_list=" . $sms_api->id_list . ""], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить дубликаты', ["delete-dublicate?id_list=" . $sms_api->id_list . "&status=" . $sms_api->status . ""], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Удалить дубликаты (ВСЕ)', ["delete-dublicate-all"], ['class' => 'btn btn-success']) ?>

        <button class="btn btn-danger btn-xs sms-api-del-dublicate-button" data-id_list="<?= $sms_api->id_list ?>">
            УДАЛИТЬ ДУБЛИКАТЫ </span></button>
        <button class="btn btn-danger btn-xs sms-api-save-button"
                data-id_list="<?= $sms_api->id_list ?>"
                data-dot_text_sms="<?= $text_sms ?>"
                data-status="<?= $sms_api->status ?>">
            Сохранить</span></button>

    </p>
    <?php


    $items = ArrayHelper::map(\app\models\SmsApi::find()->all(), 'id_list', 'name');

    $param = ['options' => [$sms_api->id_list => ['selected' => true]]];


    ?>
    <h1><?= Html::encode($this->title) ?></h1>
</div>
<?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<?php $form = ActiveForm::begin([
    'method' => 'get',
    'action' => 'edit'
]) ?>


<div class="row">
    <div class="col-sm-4">Название списка смс
        <?php echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'name' => 'id_list',
            'value' => $sms_api->id_list,
            'placeholder' => 'список',
            'options' => \app\models\SmsApi::getMyLists(),
            'label' => '',
            'color' => 'primary'
        ]); ?>
    </div>


    <?php
    $count0 = SmsApi::find()
        ->where(['id_list' => $sms_api->id_list])
        ->andFilterWhere(['status' => 0])->count();
    $count1 = SmsApi::find()
        ->where(['id_list' => $sms_api->id_list])
        ->andFilterWhere(['status' => 1])->count();
    $count2 = SmsApi::find()
        ->where(['id_list' => $sms_api->id_list])
        ->andFilterWhere(['status' => 2])->count();
    $count3 = SmsApi::find()
        ->where(['id_list' => $sms_api->id_list])
        ->andFilterWhere(['status' => 3])->count();
    ?>

    <div class="col-sm-4"> Статус
        <?php echo \app\components\MdbSelect::widget([
            'request_type' => 'get',
            'name' => 'status',
            'value' => $sms_api->status,
            'placeholder' => 'status',
            'options' => [
                '0' => "Нередактированные (" . $count0 . ")",
                '1' => "Сохраненные (" . $count1 . ")",
                '2' => "Отложенные (" . $count2 . ")",
                '3' => "Посланные (" . $count3 . ")"],
            'label' => '',
            'color' => 'primary'
        ]); ?>
    </div>
</div>
<div class="md-form">
    {name}{price}{address}
    <textarea type="text" name="text_sms" id="form76" class="md-textarea"><?= $_GET['text_sms'] ; ?></textarea>
    <label for="form76" class="">Текст смс</label>
</div>


<?= Html::submitButton('Обновить', ['class' => 'btn btn-primary']) ?>


<?php ActiveForm::end(); ?>

</div>

<? // var_dump($salelist->list_of_ids); ?>


<div class="container">
    <table class='table table-striped'>
        <thead>
        <tr>
            <th># комнат</th>
            <th>S</th>
            <th>текст</th>
            <th>Адрес</th>
            <th>Цена</th>
            <th>Пл.</th>
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


                    <td><?php $text_sms_edited = str_replace("{name}", $tr['person'], $text_sms);
                        $text_sms_edited = str_replace("{price}", $tr['price'], $text_sms_edited);
                        $text_sms_edited = str_replace("{address}", $tr['address'], $text_sms_edited);

                        echo $text_sms_edited;
                        // echo $tr['text_sms'];
                        ?>

                    </td>
                    <td><?php echo $tr['address']; ?>

                    </td>
                    <td><?php echo $tr['price']; ?></td>

                    <td><?php echo $tr['person']; ?></td>
                    <td><?php echo $tr['phone']; ?></td>
                    <td>


                        <button class="btn btn-danger btn-sm sms-api-del-button" data-id="<?= $tr['id'] ?>"
                                data-id_list="<?= $sms_api['id_list'] ?>"><span class="fa fa-minus"></span>
                        </button>
                        <button class="btn btn-danger btn-sm sms-api-delay-button"
                                data-id="<?= $tr['id'] ?>"
                                data-id_list="<?= $sms_api['id_list'] ?>"><span
                                    class="fa fa-hourglass"></span></button>

                        <button class="btn btn-danger btn-sm sms-api-ban-button"
                                data-phone="<?= $tr['phone'] ?>"
                        ><span class="fa fa-ban"></span></button>


                    </td>
                </tr>
            <?php endforeach;
        } ?>

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
