<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 26.07.2018
 * Time: 2:50
 */

namespace app\widgets;

use yii\base\Widget;

class MdbNavBar extends Widget
{
    public function run()
    {
        return $this->render('mdb/_nav-bar');
    }

}