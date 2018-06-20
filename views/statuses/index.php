<?php
use app\components\Mdb;


$params = [
    0 => [1, 'sale', 'moderated', 1, 'address-book'],
    1 => [1, 'sync', 'load_analized', 2, 'envelope-open'],
    2 => [34090, 'TEMPLATE', 'ONCONTROL', 94, 'free-code-camp'],
    3 => [34090, 'TEMPLATE', 'ONBLACK', 94, 'ban'],
];
?>

<div class="btn-group">
    <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true"
            aria-expanded="false">
    </button>
    <div class="dropdown-menu">
        <?php
        foreach ($params as $param) { ?>
            <div class="dropdown-item">
                <?= Mdb::ChangingStatuses($param[0], $param[1], $param[2], $param[3], ['fa' => $param[4]]); ?>
            </div>
            <div class="dropdown-divider"></div>
        <? } ?>

    </div>
</div>

<i class="fa fa-user fa-2x"></i>