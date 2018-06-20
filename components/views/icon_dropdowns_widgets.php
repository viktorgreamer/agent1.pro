<?php

?>


<div class="btn-group">
        <button type="button" class="btn btn-<?=$color; ?> dropdown-toggle px-3" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-icons2">
            <ul class= "horizontal">
                <?php foreach ($list as $item) { ?>
                              <li class='horizontal'><?= $item; ?></li>
              <?php } ?>
            </ul>

        </div>
</div>