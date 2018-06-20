<?php
/* @var $geocodation app\models\Geocodetion */

?>
<div class="row">
    <div class="col-6">
        <table class="table-bordered table-fixed container-fluid">
            <thead class="mdb-color darken-3">
            <tr class="text-white">
                <td>Параметр</td>
                <td>Значение</td>
            </tr>
            </thead>
            <?php $params = ['module_text','AdministrativeAreaName','locality','street','precision','coords_x', 'coords_y',
                'ThoroughfareName', 'TrimmedThoroughfareName','house','hull','fulladdress','district','geocodated_status', 'address', 'id_address','map','mapString', 'link','linkCoords', 'log']; ?>
            <?php foreach ($params as $param) { ?>
            <tr <? if (empty($geocodation[$param])) echo "class = 'table-danger'"; ?>>
                <td>
                    <b>  <?= $param ?> </b>
                </td>
                <td>
                    <?= $geocodation[$param]; ?>
                </td>
                <?php } ?>
            </tr>
        </table>
    </div>
</div>