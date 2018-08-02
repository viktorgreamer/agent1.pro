<?php

namespace app\modules\programmer\controllers;

use app\models\Sale;
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
        info(" ОБЪЯВДЕНИЙ В БАЗЕ ".Stats::countSale(['disactive' => Sale::ACTIVE]));

        return $this->render('index');
    }
    public function actionDisactiveCheck()
    {

         info(" ОБЪЯВДЕНИЙ В БАЗЕ ".Stats::countSale(['disactive' => NULL]));

        return $this->render('index');
    }




}
