<?php
?>


<form >
    <!-- Material unchecked -->
    <div class="form-check">
        <input type="checkbox" class="form-check-input" name='checkbox' id="materialUnchecked">
        <label class="form-check-label" for="materialUnchecked">Material unchecked</label>
    </div>
    <div class="form-check">
        <?php echo \yii\helpers\Html::checkbox('checkbox_yii',false,['class' => 'form-check-input','id' => 'materialUnchecked1']) ?>
        <label class="form-check-label" for="materialUnchecked1">Material unchecked</label>
    </div>

    <?php echo \yii\helpers\Html::submitButton("SUBBMIT"); ?>
</form>
