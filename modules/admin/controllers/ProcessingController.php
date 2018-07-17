<?php

namespace app\modules\admin\controllers;

use app\models\Notifications;
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
        if ($sync->TimeBetween(time(),5000)) info(" DATE START BEETWEEN",SUCCESS);
        else {
            info(" DATE START OUT",DANGER);
        }
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
