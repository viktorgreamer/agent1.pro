<?php

namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;



/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.09.2017
 * Time: 11:56
 */
class MDSideNav extends Widget
{
    public function run()
    {
            return $this->render('mdb/side-nav-view');

    }

}