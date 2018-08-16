<?php
$form = \yii\widgets\ActiveForm::begin();

?>
<div class="row">
    <div class="col-lg-6">
        PROXY (через :)
        <input type="text" name="proxy" value="<?= $_POST['proxy'] ;?>" >
    </div>
    <div class="col-lg-6">
        URL
        <input type="text" name="url" value="<?= $_POST['url'] ;?>" >

    </div>


</div>
<?php echo \yii\helpers\Html::submitButton('Проверить'); ?>
<?php \yii\widgets\ActiveForm::end();?>
ОТВЕТ
<h3><?= $message; ?></h3>
<?= $response ;?>

