<?php

namespace  app\components\webWidgets;


use yii\base\Widget;

class AdvicesWidgets extends Widget

{
    public $count = 10;
    public $type = null;
    public $not_q = null;


    public function run()
    {

        return $this->render('advices',
            [
                'count' => $this->count,
                'type' => $this->type,
                'not_q' => $this->not_q,
            ]);
    }
}