<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.09.2017
 * Time: 13:48
 */

namespace app\components;


use yii\base\Widget;


class MdbTextInput extends Widget {
    public $request_type = 'get';
    public $id;
    public $name;
    public $value;
    public $class = 'md-form';
    public $label = 'Параметр';
    public $color = '';
    public function run()
    {

        return $this->render('mdb/text-input',
            [
                'request_type' => $this->request_type,
                'name' => $this->name,
                'value' => $this->value,
                'id' => $this->id,
                'label' => $this->label,
                'color' => $this->color,
            ]);
    }

}
