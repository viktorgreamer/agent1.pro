<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.09.2017
 * Time: 13:48
 */

namespace app\components;


use yii\base\Widget;

class MdbActiveSelect extends Widget
{
    public $request_type = 'get';
    public $model;
    public $name;
    public $value;
    public $id;
    public $multiple = false;
    public $placeholder = '';
    public $options = [
        0 => 'Выбор один',
        2 => 'Выбор два',
    ];
    public $label = 'Параметр';
    public $color = '';
    public function init()
    {
        $model = $this->model;
        $this->value = $this->model[$this->name];
        $this->label = $model->attributeLabels()[$this->name];
        parent::init();
    }


    public function run()
    {

        return $this->render('mdb/mdb-active-select',
            [
                'request_type' => $this->request_type,
                'name' => $this->name,
                'value' => $this->value,
                'multiple' => $this->multiple,
                'placeholder' => $this->placeholder,
                'options' => $this->options,
                'id' => $this->id,
                'label' => $this->label,
                'color' => $this->color,
            ]);
    }

}
