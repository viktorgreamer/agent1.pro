<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 04.08.2018
 * Time: 17:16
 */

namespace app\widgets;

use yii\base\Widget;

class SaleFilterImage extends Widget
{
    public $id;

    public function run()
    {
        return $this->render('_setImage',
            [
                'id' => $this->id
            ]
        );
    }
}