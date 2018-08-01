<?php

namespace app\modules\programmer\controllers;

use app\models\Control;
use app\models\Notifications;
use app\models\ParsingConfiguration;
use app\models\Renders;
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

    public function actionCheckLost()
    {
        $module_id = 1;
        $module = Control::findOne(1);
        $module->checkLost();
        return $this->render('index');
    }

    public
    function actionTestTimeBetween()
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

    public
    function actionTestAddressChanged()
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


    public
    function actionAutoSetSimilar($id = 0)
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


    public function actionSearchDuplicates() {

       // $this->layout = "@app/views/layouts/main";
        info(" Search duplucates",SUCCESS);
        $debug_status = 'JJHKH';
        $limit = 100;
        $id_sources = 5;
        $query = Synchronization::find()->where(['<>','debug_status',$debug_status])->andWhere(['id_sources' => $id_sources]);
        info(" LOST = ".$query->count());
        if ($sales = $query->limit($limit)->all()) {
            foreach ($sales as $sale) {
                $duplicates = Synchronization::find()
                    ->where(['id_sources' => $id_sources])
                    ->andWhere(['id_in_source' => $sale->id_in_source])
                    ->andWhere(['<>','id', $sale->id])
                    ->all();
            if ($duplicates) {
                info(" ID_IN_SOURCE =".$sale->id_in_source." has ".count($duplicates));
                echo Renders::StaticView('sale/_mini-sale',['sale' => $sale,'contacts' => true]);

                foreach ($duplicates as $duplicate) {
                    if ($duplicate->date_start < $sale->date_start) {
                        info("DELETE SALE",SUCCESS);
                        $sale->delete();
                        continue;
                        Sale::deleteAll(['id' => $sale->id]);
                    } else {
                        info("DELETE DUPLICATE",SUCCESS);
                        $duplicate->delete();
                        Sale::deleteAll(['id' => $duplicate->id]);
                    }
                    info(" ID_IN_SOURCE =".$duplicate->id_in_source);
                      echo Renders::StaticView('sale/_mini-sale',['sale' => $duplicate,'contacts' => true]);
                }
                   echo "<hr>";


            }
                $sale->debug_status = $debug_status;
                $sale->save();
            }


        }
        return $this->render('index');
    }

    public function actionSendEmail() {
        Notifications::Mail("SDSDSDSDS",'an.viktory@gmail.com');
    }

}
