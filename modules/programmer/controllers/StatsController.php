<?php

namespace app\modules\programmer\controllers;

use app\models\Stats;

class StatsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionAll()
    {

        info(" АГЕНТОВ В ВЕЛИКОМ НОВГОРОДЕ ".Stats::AgentsCount());
        info(" Столько объявлений сейчас от  соственники В ВЕЛИКОМ НОВГОРОДЕ ".Stats::HouseKeepersCount());

        return $this->render('index');
    }


}
