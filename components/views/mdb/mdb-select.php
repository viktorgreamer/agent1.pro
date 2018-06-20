<?php
if (empty($_GET[$name])) {
    if ($value) {
        if ($multiple) {
            if (is_array($value)) $_GET[$name] = $value; else $_GET[$name] = $value;
        } else $_GET[$name] = $value;
    }
    else {
        if ($multiple) $_GET[$name] = [];
    }

}
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
    <option value="" disabled selected><?= $placeholder; ?></option>
    <?php
    foreach ($options as $key => $option) {
        if (($multiple) and (is_array($_GET[$name])) and (!empty( $_GET[$name])))
        {
            if (in_array($key, $_GET[$name])) $selected = " selected"; else $selected = '';

        } elseif ($key == $_GET[$name]) $selected = " selected"; else $selected = '';
        ?><option value="<?= $key ?>"<?= $selected ?>><?= $option ?></option><?php

    }
    ?>
</select>
<label><?= $label ?></label>

