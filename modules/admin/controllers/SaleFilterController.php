<?php

namespace app\modules\admin\controllers;

use app\models\Notifications;
use app\models\Renders;
use app\models\Sale;
use app\models\Tags;
use app\models\SaleFilters;
use app\models\SaleQuery;
use app\models\SaleSearch;
use Codeception\Command\SelfUpdate;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class SaleFilterController extends \yii\web\Controller
{

    public function init()
    {

        $this->layout = "@app/modules/admin/views/layouts/main.php";
        parent::init();

        // custom initialization code goes here
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCheckForSalefilter()
    {

        /* @var $salefilter \app\models\SaleFilters */
        /* @var $sale \app\models\Sale */

        info("actionCheckForSalefilter", SUCCESS);
        $salefilter = SaleFilters::findOne(229);
        echo Renders::StaticView('sale/salefilters/_view.php', ['model' => $salefilter]);
        $query = new SaleQuery();
        $query->search($salefilter);
        $dataprovider = new ActiveDataProvider(['query' => $query]);
        echo implode(",", ArrayHelper::getColumn($dataprovider->getModels(), 'id'));

        $sales = Sale::find()
            ->from(['s' => Sale::tableName()])
        // присоединяем связи
        ->joinWith(['agent AS agent'])
        ->joinWith(['addresses AS address'])
        // подлючение связи контроля
        ->joinWith(['similar AS sim'])
           // ->where(['in', 's.id', [5115, 5121, 5123, 5124, 5126, 5130, 5132, 5138, 11186, 11367, 11485, 16322, 32388, 29270, 29355, 29815, 30013, 30245, 30247]])->all();
       ->where(['in', 's.id',[11367]])->all();
        info(" INPUT COUNT =" . $query->count(), DANGER);
        info(" RESULT COUNT =" . count($sales), SUCCESS);


        if ($onControls = $salefilter->getOnControls()->all()) {
            foreach ($onControls as $control) {
                info("SALEFILTER HAS getOnControls id_similar=" . $control->id_similar . " price =" . $control->price);
            }

        }

        if ($salefilter->plus_tags) info(" PLUS TAGS =".$salefilter->plus_tags);
        if ($salefilter->minus_tags) info(" MINUS TAGS =".$salefilter->minus_tags);
        $plus_tags = Tags::convertToArray($salefilter->plus_tags);

        if (empty(\Yii::$app->cache->get('tags'))) {
            \Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());

        }
        $all_tags = \Yii::$app->cache->get('tags');

        $all_tags =  array_filter($all_tags, function ($tag) use ($plus_tags) {
            return in_array($tag['id'],$plus_tags);
        });
      //  my_var_dump($all_tags);


        if ($sales) {
            foreach ($sales as $sale) {

                //   $sale->SalefiltersCheck();
                echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                if ($salefilter->Is_in_salefilter($sale)) {
                    info(" SALE IS IN SALE FILTER ", SUCCESS);
                    $message = $sale->inform_message;
                    $salefilter->notify($message);
                   // Notifications::VKMessage(strip_tags($sale->renderLong_title()));
                   // Notifications::Mail(strip_tags($sale->renderLong_title()));

                }     else    {
                    info(" SALE IS NOT IN SALE FILTER ", DANGER);
                    echo "<hr>";
                }



            }
        }

        return $this->render('index');
    }
}