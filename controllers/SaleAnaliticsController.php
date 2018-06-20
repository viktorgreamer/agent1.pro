<?php

namespace app\controllers;


use app\models\Addresses;
use app\models\SaleFilters;
use app\models\SaleHistory;
use app\models\Analitics;
use app\models\Synchronization;
use Yii;
use app\models\Sale;
use app\models\SaleSearch;
use app\models\SaleAnaliticsAddress;
use app\models\SaleAnalitics;
use app\models\SaleAnaliticsSameAddress;
use app\models\AnaliticsModels;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;


/**
 * SaleController implements the CRUD actions for Sale model.
 */
class SaleAnaliticsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {


        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],


            ],
        ];
    }


    /**
     * Lists all Sale models.
     * @return mixed
     *

     */
    public function actionIndex()
    {
        $session = Yii::$app->session;
        $searchModel = new SaleSearch();
        $salefilter = new SaleFilters();

        $salefilter->validate(Yii::$app->request->get());
        $salefilter->load(Yii::$app->request->get());
        $exceptions = $_GET['exception'];
        $query = $searchModel->search_investigation($salefilter, ['rooms_count']);
        $salefilter->user_id = $session->get('user_id');
        $_SESSION['salefilter'] = $salefilter;

        return $this->render('investigation', [
            'salefilter' => $salefilter,
            'query' => $query,
        ]);
    }

    public function actionShow($id)
    {
        $sale = Sale::findOne($id);
        if ($sale->is_full()) {
            // SaleAnalitics
            $SaleAnalitics = New SaleAnalitics();
            $SaleAnalitics->ExportParametersFromSale($sale);
            $SaleAnalitics->CalculateStatistic();

            // SaleAnaliticsAddress
            $SaleAnaliticsAddress = New SaleAnaliticsAddress();
            $SaleAnaliticsAddress->ExportParametersFromSale($sale);
            $SaleAnaliticsAddress->CalculateStatisticAddress();

            // SaleAnaliticsSameAddress
            $SaleAnaliticsSameAddress = New SaleAnaliticsSameAddress();
            $SaleAnaliticsSameAddress->ExportParametersFromSale($sale);
            $SaleAnaliticsSameAddress->CalculateStatisticSameAddress();


        }
        return $this->render('show-full-statistic', compact(['sale', 'SaleAnaliticsSameAddress', 'SaleAnalitics', 'SaleAnaliticsAddress']));
    }

    public function actionCreateGraph()
    {
//        $S_min = SaleHistory::find()->min('grossarea');
//        $S_max = SaleHistory::find()->max('grossarea');
//        $rooms_count = 1;
// валидация данных
//        $rooms_count = $_GET['rooms_count'];
//        $S_max = $_GET['S_max'];
//        $S_min = $_GET['S_min'];
//        $floorcount = $_GET['floorcount'];

        $filter = new Analitics();
        $filter->floorcount = (Yii::$app->request->get('floorcount'));
        $filter->house_type = (Yii::$app->request->get('house_type'));
        $filter->S_max = (Yii::$app->request->get('S_max'));
        $filter->S_min = (Yii::$app->request->get('S_min'));
        $filter->rooms_count = (Yii::$app->request->get('rooms_count'));
        // my_var_dump($filter);

        $data = [];
        $avarage_price = [];
        $ss = [];

        if ($filter->validate(Yii::$app->request->get())) {
            for ($s = $filter->S_min; $s <= $filter->S_max; $s++) {
                $count = Synchronization::find()
                    ->Where(['rooms_count' => $filter->rooms_count])
                    ->andFilterWhere(['floorcount' => $filter->floorcount])
                    ->andFilterWhere(['between', 'grossarea', $s, $s + 0.999])
                    ->andFilterWhere(['house_type' => $filter->house_type])
                    ->groupBy('id_address,floor')
                    ->count();

                $avarage = round(Synchronization::find()
                    ->andWhere(['rooms_count' => $filter->rooms_count])
                    ->andFilterWhere(['floorcount' => $filter->floorcount])
                    ->andFilterWhere(['between', 'grossarea', $s, $s + 0.999])
                    ->andFilterWhere(['house_type' => $filter->house_type])
                    ->groupBy('id_address,floor')
                    ->average('price'));
                array_push($ss, (int)$s);
                array_push($data, (int) $count);
                array_push($avarage_price, $avarage);

            }
        }

        if (count($data) == 0) $message = "нет данных";

        return $this->render('create-graph', [
            'message' => $message,
            'filter' => $filter,
            's_max' => $filter->S_max,
            's_min' => $filter->S_min,
            'data' => $data,
            'ss' => $ss,
            'avarage_price' => $avarage_price]);


    }


    public function actionSuperFilter()
    {
        $superfilter = new SaleFilters();
        $superfilter->load(Yii::$app->request->get());
        $period_start = $superfilter->date_start;
        $period_finish = $superfilter->date_finish;

        $discount = $superfilter->discount;
        $array_of_interested_id = [];
        $sales_for_analize = Sale::find()// выбираем для анализа только объяаления за период
        ->where(['>', 'date_start', (time() - $period_start * 86400)])
            ->andfilterwhere(['<', 'date_start', (time() - $period_finish * 86400)])
            ->all();
        if ($sales_for_analize) { // если есть, что анализировать то пробегаемся по всем записям
            foreach ($sales_for_analize as $item) {
                // формируем массив этажей
                /* $array_of_floors = [];
                 if (($item['floor'] == 1) or ($item['floor'] == $item['floorcount'])) { // если первый этаж или последний
                     $array_of_floors = [1, $item['floorcount']];
                 } else {
                     for ($i_floor = 2; $item['floorcount'] - 1; $i_floor++) {
                         $array_of_floors += [$i_floor];
                     }
                 if (1) {
                     var_dump($array_of_floors);
                     exit;
                 } break;
                 }*/
                // вычисляем среднюю цену под вариантам похожих этажей такого же метража и этажности и типа дома такойже площади
                if (($item['floorcount'] != 0) and ($item['grossarea'] != 0)) {
                    $avg_price = SaleHistory::find()
                        ->filterWhere(['rooms_count' => $item['rooms_count']])
                        //->andFilterWhere(['in', 'floor', [1,2,3,4,5,6,7,8,9]])
                        ->andFilterWhere(['floorcount' => $item['floorcount']])
                        ->andFilterWhere(['grossarea' => $item['grossarea']])
                        ->average('price');

                    $avg_price_address = SaleHistory::find()
                        ->filterWhere(['rooms_count' => $item['rooms_count']])
                        //->andFilterWhere(['in', 'floor', [1,2,3,4,5,6,7,8,9]])
                        ->andFilterWhere(['like', 'address', $item['address']])
                        ->average('price');

                } else {
                    $avg_price = SaleHistory::find()
                        ->filterWhere(['rooms_count' => $item['rooms_count']])
                        //->andFilterWhere(['in', 'floor', [1,2,3,4,5,6,7,8,9]])
                        ->andFilterWhere(['like', 'address', $item['address']])
                        ->average('price');
                    $avg_price_address = $avg_price;

                }
                if (($item['rooms_count'] == 30) and $item['grossarea'] > 27) {
                    $avg_price = SaleHistory::find()
                        ->filterWhere(['rooms_count' => $item['rooms_count']])
                        //->andFilterWhere(['in', 'floor', [1,2,3,4,5,6,7,8,9]])
                        ->andFilterWhere(['like', 'address', $item['address']])
                        ->average('price');
                    $avg_price_address = $avg_price;
                }
                if ($item['rooms_count'] == null) $avg_price;


                if (($item['price'] < (100 - $discount) * $avg_price / 100) and ($item['price'] > 0.5 * $avg_price)) {
                    array_push($array_of_interested_id, $item['id']);
                    $avg_price_array[$item['id']] = $avg_price;;
                    $avg_price_address_array[$item['id']] = $avg_price_address;;
                }

            }
        }

        $searchModel = new SaleSearch();
        $data = $searchModel->search_sale_id($array_of_interested_id);


        return $this->render('search', [
            'avg_price_array' => $avg_price_array,
            'avg_price_address' => $avg_price_address_array,
            'superfilter' => $superfilter,

            'data' => $data['data'],
            'pages' => $data ['pages'],

        ]);


    }


    public function actionShowNearestSameObjects($id)
    {
        $sale = Sale::findOne($id);


        return $this->render('same-objects', [
            'saleshistory' => $sale->get_nearest_same_address_objects($sale->radius)

        ]);


    }

    public function actionShowSameAddressObjects($id)
    {
        $sale_history = Sale::findOne($id);


        return $this->render('same-objects', [
            'saleshistory' => $sale_history->get_same_address_objects()

        ]);


    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */


    public function actionSearchBySuperFilterOld()
    {

        $session = Yii::$app->session;

        $input_id_super_filter = new SaleFilters();
        $input_id_super_filter->load(Yii::$app->request->get());
        // если get параметры пусты то фильтруем по первому попавшемуся фильтру
        if ($input_id_super_filter->id == 0) {
            $input_id_super_filter = SaleFilters::find()
                ->where(['is_super_filter' => 1])
                ->andwhere(['user_id' => $session->get('user_id')])
                ->one();

        }
        $superfilter = SaleFilters::findOne($input_id_super_filter->id);
        $period_start = $superfilter->date_start;
        $period_finish = $superfilter->date_finish;
        // Echo "это список black list".$superfilter->black_list_id;


        $array_of_interested_id = [];
        // если выбрать показать все то стандартный фильтр
        if ($_GET['type_of_show'] == 'all') {

            $sales_for_analize = Sale::find()// выбираем для анализа только объяаления за период
            ->where(['>', 'date_start', (time() - $period_start * 86400)])
                ->andfilterwhere(['<', 'date_start', (time() - $period_finish * 86400)])
                ->all();

            if ($sales_for_analize) { // если есть, что анализировать то пробегаемся по всем записям
                foreach ($sales_for_analize as $item) {
                    $sale_for_analize = Sale::findOne($item['id']);

                    if ($superfilter->is_in_superfilter($item['id'])) array_push($array_of_interested_id, $item['id']);
                }

            }

            $array_of_interested_id = array_merge($array_of_interested_id, explode(",", $superfilter->white_list_id));

        } else if ($_GET['type_of_show'] == 'starred') { // если выбран показать только white list
            $array_of_interested_id = explode(",", $superfilter->white_list_id);
        } else if ($_GET['type_of_show'] == 'banned') { // если выбран показать только black list
            $array_of_interested_id = explode(",", $superfilter->black_list_id);
        } else if ($_GET['type_of_show'] == 'not_starred') { // если выбран показать новые обьявления неотмеченные звездочкой
            $sales_for_analize = Sale::find()// выбираем для анализа только объяаления за период
            ->where(['>', 'date_start', (time() - $period_start * 86400)])
                ->andfilterwhere(['<', 'date_start', (time() - $period_finish * 86400)])
                ->all();
            if ($sales_for_analize) { // если есть, что анализировать то пробегаемся по всем записям
                foreach ($sales_for_analize as $item) {
                    $sale_for_analize = Sale::findOne($item['id']);


                    // если данный item в black листе или какой то из его дублтикатов ирр то выходим
//                    echo "это id ".$item['id'];
//                    echo "<br>";
//                 echo "это результат функции ".$superfilter->is_in_black_list($item['id'], $item['id_irr_duplicate']);
//                    echo "<br>";
                    if ($superfilter->is_in_superfilter($item['id'])) array_push($array_of_interested_id, $item['id']);
                }

            }
            $array_of_interested_id = array_diff($array_of_interested_id, explode(",", $superfilter->white_list_id));
        }

        $searchModel = new SaleSearch();
        // осуществляем простой поиск по списку id
        $data = $searchModel->search_sale_id($array_of_interested_id);


        return $this->render('search-by-super-filter', [
            'avg_price_array' => $avg_price_array,
            'avg_price_address' => $avg_price_address_array,
            'avg_price_count' => $avg_price_array_count,
            'avg_price_address_count' => $avg_price_address_array_count,
            'superfilter' => $superfilter,

            'data' => $data['data'],
            'pages' => $data ['pages'],

        ]);


    }

    public function actionSearchBySuperFilter()
    {

        $session = Yii::$app->session;

        $input_id_super_filter = new SaleFilters();
        $input_id_super_filter->load(Yii::$app->request->get());
        // если get параметры пусты то фильтруем по первому попавшемуся фильтру
        if ($input_id_super_filter->id == 0) {
            $input_id_super_filter = SaleFilters::find()
                ->where(['is_super_filter' => 1])
                ->andwhere(['user_id' => $session->get('user_id')])
                ->one();

        }
        $superfilter = SaleFilters::findOne($input_id_super_filter->id);
        $period_start = $superfilter->date_start;
        $period_finish = $superfilter->date_finish;
        // Echo "это список black list".$superfilter->black_list_id;


        $array_of_interested_id = [];
        // если выбрать показать все то стандартный фильтр
        if ($_GET['type_of_show'] == 'all') {

            $sales_for_analize = Sale::find()// выбираем для анализа только объяаления за период
            ->where(['>', 'date_start', (time() - $period_start * 86400)])
                ->andfilterwhere(['<', 'date_start', (time() - $period_finish * 86400)])
                ->all();

            if ($sales_for_analize) { // если есть, что анализировать то пробегаемся по всем записям
                foreach ($sales_for_analize as $item) {
                    $sale_for_analize = Sale::findOne($item['id']);

                    if ($superfilter->is_in_superfilter($item['id'])) array_push($array_of_interested_id, $item['id']);
                }

            }

            $array_of_interested_id = array_merge($array_of_interested_id, explode(",", $superfilter->white_list_id));

        } else if ($_GET['type_of_show'] == 'starred') { // если выбран показать только white list
            $array_of_interested_id = explode(",", $superfilter->white_list_id);
        } else if ($_GET['type_of_show'] == 'banned') { // если выбран показать только black list
            $array_of_interested_id = explode(",", $superfilter->black_list_id);
        } else if ($_GET['type_of_show'] == 'not_starred') { // если выбран показать новые обьявления неотмеченные звездочкой
            $sales_for_analize = Sale::find()// выбираем для анализа только объяаления за период
            ->where(['>', 'date_start', (time() - $period_start * 86400)])
                ->andfilterwhere(['<', 'date_start', (time() - $period_finish * 86400)])
                ->all();
            if ($sales_for_analize) { // если есть, что анализировать то пробегаемся по всем записям
                foreach ($sales_for_analize as $item) {
                    $sale_for_analize = Sale::findOne($item['id']);


                    // если данный item в black листе или какой то из его дублтикатов ирр то выходим
//                    echo "это id ".$item['id'];
//                    echo "<br>";
//                 echo "это результат функции ".$superfilter->is_in_black_list($item['id'], $item['id_irr_duplicate']);
//                    echo "<br>";
                    if ($superfilter->is_in_superfilter($item['id'])) array_push($array_of_interested_id, $item['id']);
                }

            }
            $array_of_interested_id = array_diff($array_of_interested_id, explode(",", $superfilter->white_list_id));
        }

        $searchModel = new SaleSearch();
        // осуществляем простой поиск по списку id
        $data = $searchModel->search_sale_id($array_of_interested_id);


        return $this->render('search-by-super-filter', [
            'salefilter' => $superfilter,

            'data' => $data,

        ]);


    }


    function actionFindNearestSameIdAddresses()
    {
        $id = 10;
        $city = 'Великий Новгород';
        // Yii::app()->params['city'] = $city;
        $prefix = 'test';
        Addresses::setTablePrefix($prefix);
        $address = Addresses::findOne($id);

        $same_addresses = Addresses::find()
            ->where(['locality' => $address->locality])
            ->andwhere(['<=', 'floorcount', $address->floorcount + 1])
            ->andwhere(['>=', 'floorcount', $address->floorcount - 1])
            ->andwhere(['house_type' => $address->house_type])
            ->andwhere(['>=', 'year', $address->year - 5])
            ->andwhere(['<=', 'year', $address->year + 5])
            ->all();
        echo "<br>  от этого адреса " . $address->address . " - " . $address->year;
        foreach ($same_addresses as $same_address) {

            if ($address->is_in_this_radius($same_address, 300)) {
                echo "<br> данный адрес похохий адрес " . $same_address->address . " " . $same_address->year . " - на расстоянии " . $address->getDistance($address->coords_x, $address->coords_y, $same_address->coords_x, $same_address->coords_y) . " метров.";
            }


        }
        return $this->render('index');

    }

    protected
    function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
