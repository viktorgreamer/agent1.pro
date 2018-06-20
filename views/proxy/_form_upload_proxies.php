<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Proxy */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="proxy-form">
<?= $body; ?>
    <?php $form = ActiveForm::begin(); ?>
<?php echo Html::textarea('body', $body,['class' => 'md-form']); ?>
    <div class="form-group">
        <?= Html::submitButton('upload',['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
