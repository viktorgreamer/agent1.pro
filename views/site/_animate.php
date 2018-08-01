<?php

?>
<table class="table">
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">First</th>
        <th scope="col">Last</th>
        <th scope="col">Handle</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <th scope="row">1</th>
        <td><?php echo \app\models\Renders::Counter("avito", 0, 3100, 3) ?></td>
        <td><?php // echo \app\models\Renders::Counter("cian", 0, 6000, 3) ?></td>
        <td><?php  echo \app\models\Renders::Counter("irr", 0, 1600, 3) ?></td>
    </tr>
    <tr>
        <th scope="row">1</th>
        <td><?php // echo \app\models\Renders::Counter("avito1", 0, 3100, 3) ?></td>
        <td><?php//  echo \app\models\Renders::Counter("cian2", 0, 6000, 3) ?></td>
        <td><?php//  echo \app\models\Renders::Counter("irr3", 0, 1600, 3) ?></td>
    </tr>

    </tbody>
</table>
