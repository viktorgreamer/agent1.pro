<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;

?>


<?php Pjax::begin([
    'id' => 'pjax-container',
    'timeout' => 50000, // Увеличиваем время ожидания, по умолчанию 1000
    'enablePushState' => false, // Убираем отображение адреса запроса, по умолчанию true
]); ?>
<?= Html::beginForm(["addresses/quick-search?id=".$id], 'post', ['data-pjax' => 0, 'class' => 'form-inline']); ?>
<?= Html::input('text', 'address', Yii::$app->request->post('address'), ['class' => 'form-control']) ?>
<?= Html::submitButton('поиск адреса', ['class' => 'btn btn-primary', 'name' => 'hash-button']) ?>
<?= Html::endForm() ?>
<?php if ($addresses) {
    // my_var_dump($addresses);
    foreach ($addresses as $address) {
        echo "<br><button class=\"btn btn-xs btn-success set-id-address \" data-id_address=\"" . $address->id . "\"
                                data-id=\"" . $id . "\">" . $address->address . "
                        </button>";
    }
} else echo $message;
?>


<?php Pjax::end(); ?>

