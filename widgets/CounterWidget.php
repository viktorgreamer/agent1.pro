<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 29.07.2018
 * Time: 16:40
 */

namespace app\widgets;


use yii\base\Widget;

class CounterWidget extends Widget
{
     public $id = 0;
     public $from = 0;
     public $to = 100;
     public $time = 5; // время в секундах

    public function run()
    {
        return $this->render('counter_view',[
            'from' => $this->from,
            'to' => $this->to,
            'time' => $this->time*1000,
            'id' => $this->id,
            ]);
    }

}