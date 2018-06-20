<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2017
 * Time: 23:12
 */

namespace app\components;


use yii\base\Widget;

class IconsDropdownWidgets extends Widget

{
    public $list;
    public $color = 'primary';




    public function run()
    {

        return $this->render('icon_dropdowns_widgets',
            [
                'list' => $this->list,
                'color' => $this->color,

            ]);
    }
}