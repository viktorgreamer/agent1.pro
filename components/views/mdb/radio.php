<?php
if ($request_type == 'get') $request = $_GET[$name]; else $request = $_POST[$name];
//echo "request=".$request;
?>
<div <? if ($id) echo "id=\"" . $id . "\""; ?>>
   <?php foreach ($options as $key=>$option) {
       ?>
       <div class="form-group">
           <input <? if ($name) echo "name=\"" . $name . "\""; ?> type="radio" <?
          if ($id) echo "id=\"" . $id . "" . ($key+1) . "\""; if ($key == $request) echo " checked=\"checked\""; ?>>
           <label for="<? if ($id) echo $id . "" . ($key+1);?>"><?= $option; ?></label>
       </div>
    <?php

   }
   ?>

</div>
