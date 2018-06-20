<?php

namespace app\components\webWidgets;

use app\components\SaleWidget;


/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 03.10.2017
 * Time: 9:29
 */
class SaleWidgets extends SaleWidget
{

    public function run()
    {
        return $this->render('sale-table');

    }

}