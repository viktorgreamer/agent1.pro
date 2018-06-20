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

        return $this->render('\mdb\radio',
            [
                'request_type' => $this->request_type,
                'name' => $this->name,
                'class' => $this->class,
                'id' => $this->id,
                'options' => $this->options,
                'label' => $this->label,
                'color' => $this->color,
            ]);
    }

}