<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.09.2017
 * Time: 13:48
 */

namespace app\components;


use yii\base\Widget;

class MdbRadio extends Widget {
    public $request_type = 'get';
    public $id;
    public $name;
    public $class = '';
    public $options;
    public $label = 'Параметр';
    public $color = '';
    public function run()
    {

        return $this->render('statuses',
            [

            ]);
    }

}