<?php

namespace app\modules\admin\controllers;

use app\models\Notifications;
use app\models\ParsingConfiguration;
use app\models\Sale;
use app\models\SaleSimilar;
use app\models\SynchronizationQuery;
use app\models\Synchronization;
use yii\helpers\Url;
use yii\helpers\Html;

class ProcessingController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionTestTimeBetween()
    {
        $sync = new Synchronization();
        $sync->date_start = time() - 8000;
        info(\Yii::$app->formatter->asRelativeTime($sync->date_start));
        if ($sync->TimeBetween(time(), 5000)) info(" DATE START BEETWEEN", SUCCESS);
        else {
            info(" DATE START OUT", DANGER);
        }
        return $this->render('index');
    }

    public function actionTestAddressChanged()
    {
        /* @var $active_item Synchronization */

        $parsing = [
            'title' => "30 м², 1-комнатная",
            'id_sources' => 2,
            'address_line' => "улица Кочетова 17",
            'url' => "https://realty.yandex.ru/offer/7260836360025826304/",
            'id_in_source' => "7260836360025826304",
            'price' => 1160000,
            'date_start' => 1531820002,
        ];
        my_var_dump($parsing);

        $checked_ids = [];
        $config = ParsingConfiguration::findOne(14);

        $cashed_items = Synchronization::getCachedCategory($config->id);
        //  my_var_dump($cashed_items);
        info(" THIS CATEGORY HAD " . count($cashed_items) . " ITEMS INSIDE YOURSELF", DANGER);

        $active_item = $cashed_items[$parsing['id_in_source']];
        my_var_dump(Synchronization::findOne($active_item->id)->renderProcessingStatuses());
        if (!in_array($active_item->id_in_source, $checked_ids)) $SynchResponse = Synchronization::TODO_NEW($parsing, $config, ['active_item' => $active_item]);
        else info(" THIS ITEM HAS BEEN CHECKED YET IN THIS CHECKING...SKIP", DANGER);

        $checked_ids[] = $SynchResponse['id_in_source'];
        info($SynchResponse['raw'], SUCCESS);

        my_var_dump(Synchronization::findOne($active_item->id)->renderProcessingStatuses());
        info(" CHECKED IDS ", SUCCESS);
        my_var_dump($checked_ids);


        info(" NEW CHECKING ", SUCCESS);
        $active_item = $cashed_items[$parsing['id_in_source']];
     //   my_var_dump(Synchronization::findOne($active_item->id)->renderProcessingStatuses());
        if (!in_array($active_item->id_in_source, $checked_ids)) $SynchResponse = Synchronization::TODO_NEW($parsing, $config, ['active_item' => $active_item]);
        else info(" THIS ITEM HAS BEEN CHECKED YET IN THIS CHECKING...SKIP", DANGER);


        //  sleep(5);


        return $this->render('index');
    }


    public function actionAutoSetSimilar($id = 0)
    {
        /* @var $sale Sale */
        /* @var $similar Sale */
        //  $_SESSION['id'] =


        if ($id == 0) {
            $query = SynchronizationQuery::find(READY_FOR_SALESIMILAR_CHECK);

            $count = $query->count();

            info("lost = " . $count, 'alert');
            $sales = $query
                // ->andWhere(['not in', 'disactive', [1, 2]])
                // ->orderBy(new Expression('rand()'))
                ->limit(10)
                // ->andFilterWhere(['tags_autoload' => 0])
                ->all();
            if (!$sales) return $this->render('index');
        } else {
            $sale = Synchronization::findOne($id);
            info(" id=" . $sale->id);
            echo $sale->renderLong_title();

            $sale->similarCheckNewer();
            echo "<hr>";
            //  $sale->save();
        }
        if ($sales) {
            foreach ($sales as $sale) {
                info(" id=" . $sale->id);
                echo "<br>" . Html::a('manual', Url::to(['processing/auto-set-similar', 'id' => $sale->id]), ['target' => '_blank']);
                echo "<br>" . $sale->renderLong_title();
                $sale->similarCheckNewer();
                //  $sale->save();
            }

        }

        return $this->render('index');
    }

}
