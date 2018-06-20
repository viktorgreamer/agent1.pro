<?php
if ($request_type == 'get') {
    if ($value) $request = $value; else  $request = $_GET[$name];
} else {
    if ($value) $request = $value; else $request = $_POST[$name];
}

?>

    <input type="text" <? if ($id) echo "id=\"" . $id . "\""; ?> <? if ($name) echo "name=\"" . $name . "\""; ?> class="form-control <?= $class; ?>" value="<?= $request; ?>">
    <label for="<? if ($id) echo $id; ?>" class=""><? if ($label) echo $label; ?></label>