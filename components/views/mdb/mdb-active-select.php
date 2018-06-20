<?
$selected = '';
$class = '';
$select_options = '';
if ($color) $class = " colorful-select dropdown-".$color;
if ($multiple) $select_options .= "name=\"".$name."[]\"";
else $select_options .= " name=\"" . $name . "\"";
if ($multiple) $select_options .= "multiple";
if ($id) $select_options .= " id='".$id."'";

?>
<select class="mdb-select<?= $class; ?>" <?= $select_options; ?>>
<?php if ($placeholder) { ?>  <option value="" disabled selected><?= $placeholder; ?></option> <?php } ?>
    <?php
    foreach ($options as $key => $option) {
        if (($multiple) and (is_array($value)) and (!empty($value)))
        {
            if (in_array($key, $value)) $selected = " selected"; else $selected = '';

        } elseif ($key == $value) $selected = " selected"; else $selected = '';
        ?><option value="<?= $key ?>"<?= $selected ?>><?= $option ?></option><?php

    }
    ?>
</select>
<label><?= $label ?></label>


<?php
$js = "$('.mdb-select').material_select();";
$this->RegisterJS($js, yii\web\View::POS_READY);
?>
