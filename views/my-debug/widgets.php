<?php

use app\models\Actions;
use app\models\SaleSimilar;
use app\models\Geocodetion;
use app\models\Sale;

// $sale = Sale::find()->where(['IS NOT', 'id_similar', NULL])->all(); ?>


<?php $body = $this->render('icons') ?>

    <!-- Split button -->
    <div class="btn-group">
        <button type="button" class="btn btn-danger dropdown-toggle px-3" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
            <span class="sr-only">Toggle Dropdown</span>
        </button>
        <div class="dropdown-menu dropdown-icons2">
            <ul class= "horizontal">
                <li class='horizontal'><i class="fa fa-pencil fa-fw fa-2x"></i></li>
                <li class='horizontal'><i class="fa fa-trash-o fa-fw fa-2x"></i></li>
                <li class='horizontal'><i class="fa fa-ban fa-fw fa-2x"></i></li>
                <li class="horizontal">
                    <a class="change-statuses" data-id="<?= $sale->id; ?>" data-model='sale'
                       data-attrname='geocodated' data-value=9><span class="fa-stack fw fa-lg">
  <i class="fa fa-map fa-stack-1x"></i>
  <i class="fa fa-ban fa-stack-2x text-danger"></i>
                </li>
            </ul>

        </div>
    </div>
<?php
