<?php

namespace app\controllers;

use app\models\Notifications;
use app\models\RealTags;
use app\models\SaleAnaliticsSameAddress;
use app\models\SaleHistory;
use app\models\SaleQuery;
use app\models\Tags;
use Yii;
use app\models\Sale;
use app\models\SaleSearch;
use app\models\SaleFilters;
use app\models\SaleLists;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Expression;


/**
 * SaleController implements the CRUD actions for Sale model.
 * @var $sale Sale;
 */
class SaleController extends Controller
{


    /**
     * Lists all Sale models.
     * @return mixed
     */
    public function beforeAction($action)
    {
        $session = Yii::$app->session;
        if (!$session['user_id']) return $this->redirect(['site/login'])->send();
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }

    public function actionTest()
    {
        info("TEST_SALE");

        return $this->render('test');
    }

    public function actionModerate($id = 0)
    {

        $session = Yii::$app->session;
        if ($id) {
            $salefilter = SaleFilters::findOne($id);
        } else $salefilter = new SaleFilters();
        $params = Yii::$app->request->get();
        if (!$salefilter->validate(Yii::$app->request->get())) echo my_var_dump($salefilter->getErrors());
        $salefilter->load(Yii::$app->request->get());

        $query = new SaleQuery();
        $query->search($salefilter, $_GET['type_of_show']);
        $query->orderBy(new Expression('rand()'));
        $salefilter->user_id = $session->get('user_id');
        $session->set('current_filter', $salefilter);

        return $this->render('moderate',
            [
                'query' => $query->limit(7),
                'salefilter' => $salefilter
            ]);



    }

    public
    function actionSearch()
    {
        $session = Yii::$app->session;
        $searchModel = new SaleSearch();
        $salefilter = new SaleFilters();
        $salefilter->load(Yii::$app->request->get());
        if ($_GET['regions'] != 10) {
            $region = SaleFilters::findOne($_GET['regions']);
            $salefilter->polygon_text = $region->polygon_text;
        }
        $salefilter->user_id = $session->get('user_id');
        $session['current_filter'] = $salefilter;
        $data = $searchModel->finalsearch($salefilter);
        if (Yii::$app->request->get('savefilter')) {
            if ($salefilter->Exists()) $session->setFlash('ExistedSaleFilter', true);
            else if (!$salefilter->save()) my_var_dump($salefilter->getErrors());
        }
        // если поставлена галочка то сохраняем список
        if (Yii::$app->request->get('savelist')) {
            info("СОЗДАНИЕ SALE LISTS");
            $salefilter->createSaleListFromSaleFilter();
        }


        return $this->render('index', [
            'salefilter' => $salefilter,
            //'dataProvider' => $dataProvider
            'data' => $data['data'],
            'pages' => $data ['pages'],
            'all_sales' => $data ['all_sales']

        ]);

    }

    // #search_by_filter
    public
    function actionSearchByFilter($id = 0)
    {
        $session = Yii::$app->session;
        if ($id == 0) {
        } else {
            $salefilter = SaleFilters::findOne($id);
            $salefilter->modifyExtendedParams();
        }
        $query = new SaleQuery();
        $query->search($salefilter, $_GET['type_of_show']);

        // если поставлена галочка то сохраняем список
        $salefilter->user_id = $session->get('user_id');
        $session->set('current_filter', $salefilter);
        if (Yii::$app->request->get('savelist')) {
            info("СОЗДАНИЕ SALE LISTS");
            $salefilter->createSaleListFromSaleFilter();
        };
        return $this->render('index',
            [
                'query' => $query,
                'salefilter' => $salefilter
            ]);
    }


    public
    function actionIndex()
    {
        $session = Yii::$app->session;
        $searchModel = new SaleSearch();
        $salefilter = new SaleFilters();

        if (!$salefilter->validate(Yii::$app->request->get())) echo my_var_dump($salefilter->getErrors());
        $salefilter->load(Yii::$app->request->get());
        // если поставлена галочка то сохраняем список


        if ($_GET['view'] != SaleFilters::VIEW_MAP) {
            $dataProvider = $searchModel->search_dataprovider($salefilter);
            //  $salefilter->user_id = $session->get('user_id');
            $session->set('current_filter', $salefilter);
            //    $session['current_filter'] = $salefilter;

            if (Yii::$app->request->get('savelist')) {
                info("СОЗДАНИЕ SALE LISTS");
                $salefilter->createSaleListFromSaleFilter();
            };
            return $this->render('index1', [
                'searchModel' => $searchModel,
                'salefilter' => $salefilter,
                'dataProvider' => $dataProvider,
            ]);
        } else {
            $salefilter = new SaleFilters();
            // $salefilter = $session['current_filter'];
            $searchModel = new SaleSearch();

            if (!$salefilter->validate(Yii::$app->request->get())) echo my_var_dump($salefilter->getErrors());
            $salefilter->load(Yii::$app->request->get());


            $sales = $searchModel->search_for_map($salefilter);

            return $this->render('index_map', ['sales' => $sales, 'salefilter' => $salefilter]);

        }

    }

    public
    function actionIndex2($id = null)
    {

        $session = Yii::$app->session;
        if ($id) {
            $salefilter = SaleFilters::findOne($id);
        } else $salefilter = new SaleFilters();
        $params = Yii::$app->request->get();
        if (!$salefilter->validate(Yii::$app->request->get())) echo my_var_dump($salefilter->getErrors());
        $salefilter->load(Yii::$app->request->get());

        $query = new SaleQuery();
        $query->search($salefilter, $_GET['type_of_show']);

        $salefilter->user_id = $session->get('user_id');
        $session->set('current_filter', $salefilter);
        /*if (Yii::$app->request->get('savelist')) {
            info("СОЗДАНИЕ SALE LISTS");
            $salefilter->createSaleListFromSaleFilter();
        };*/

        return $this->render('index',
            [
                'query' => $query,
                'salefilter' => $salefilter
            ]);


    }


    /*
    /* @var $sale app\models\Sale */

    public
    function actionShowOnMap()
    {
        $session = Yii::$app->session;
        $salefilter = $session['current_filter'];
        $searchModel = new SaleSearch();

        if (!$salefilter->validate(Yii::$app->request->get())) echo my_var_dump($salefilter->getErrors());
        $salefilter->load(Yii::$app->request->get());


        $sales = $searchModel->search_for_map($salefilter);
        if ($sales) {
            if ($sales) {
                $sales = array_group_by($sales, 'id_address');

            }
        }
        $salefilter->user_id = $session->get('user_id');


        return $this->renderAjax('_map', ['sales' => $sales]);
        //  return count($sales);
    }

    public
    function actionSaleTest()
    {

        $tags_array = [44, 52, 85, 90, 93, 96, 102];
        echo Tags::render($tags_array);
        $tags_address_array = [4, 34];
        echo Tags::render($tags_address_array);
        $query = Sale::find()
            ->where(['not in', 'disactive', [1, 2]]);
        // ->andwhere(['rooms_count' => 2])
        $query->joinWith(['tags']);
        $query->andWhere([RealTags::tableName() . '.tag_id' => $tags_array]);
        $query->groupBy("sale_id");
        $query->having(new Expression("count(*)=" . count($tags_array)));

        $query->joinWith(['address']);
        $query->andWhere([RealTags::tableName() . '.tag_id' => $tags_address_array]);
        $query->groupBy("id_address_tag");
        $query->having(new Expression("count(*)=" . count($tags_address_array)));

        $query->limit(5);
        // ->asArray()
        $sales = $query->all();
        foreach ($sales as $sale) {
            echo " <br>" . $sale->id . " " . $sale->address . " " . $sale->price . " ";

            $tags = $sale->tags;
            if ($tags) {
                foreach ($tags as $tag) {
                    echo $tag->tag_id . ", ";
                }
            }


        };

        return $this->render('test');

    }


    public
    function actionRewriteSalelist()
    {

        $session = Yii::$app->session;
        $salelist = $session['current_list'];

        $rewrited_salelist = SaleLists::findOne($salelist->FindExisted());
        $rewrited_salelist->setAttributes($salelist->getAttributes());

        $rewrited_salelist->komment = $salelist->komment;
        $rewrited_salelist->id = $salelist->FindExisted();
        if (!$rewrited_salelist->save()) return 'error';
        return 'ok';


    }

    public
    function actionRewriteSalefilter()
    {

        $session = Yii::$app->session;
        $salefilter = $session['current_filter'];
        $rewrited_salefilter = SaleFilters::findOne($salefilter->FindExisted());
        $id = $rewrited_salefilter->id;
        $rewrited_salefilter->setAttributes($salefilter->getAttributes());
        $rewrited_salefilter->komment = $salefilter->komment;
        $rewrited_salefilter->id = $id;
        if (!$rewrited_salefilter->save()) return 'error';
        return 'ok';


    }

    public
    function actionView($id)
    {
        return $this->render('views', [
            'sale' => Sale::findOne($id),
        ]);
    }

    public
    function actionAnalizeOne($id)
    {
        $sale = Sale::findOne($id);

        $message = " выбрали <h5>" . $sale->id . "Sale" . $sale->title . " cost= " . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . " year " . $sale->year . "</h5>";
        $message2 = " выбрали <h5>Sale" . $sale->title . " cost= " . $sale->price . " address= " . $sale->address . " grossarea" . $sale->grossarea . "year " . $sale->year . "</h5> <a href='" . $sale->url . "'>link</a>";
        if ($sale->is_full()) {
            $NewSaleAnalitictSameAddress = New SaleAnaliticsSameAddress();
            $array = $NewSaleAnalitictSameAddress->show_statistic_same_address($message, $sale);
            $prices = $array['prices'];
            $new_prices = $array['new_prices'];
            $simple_average_price = $array['simple_average_price'];
            $sliced_average_price = $array['sliced_average_price'];
            $average = $array['average'];
            $message = $array['message'];
            $sales_history = $array['sales_history'];
        }


        if ($new_prices) return $this->render('charts', compact(['prices', 'average', 'new_prices', 'simple_average_price', 'sliced_average_price', 'message', 'sale', 'sales_history']));
        else {
            echo $message2;
            echo " noothig to show";
            my_var_dump($array);
            return $this->render('views');

        }


    }

    /**
     * Creates a new Sale model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public
    function actionCreate()
    {
        $new_sale = new Sale();
        $session = Yii::$app->session;
        $new_sale->locality = $session->get('city');
        $new_sale->id = time() + $session->get('user_id');
        $new_sale->id_sources = 9;
        if ($new_sale->load(Yii::$app->request->post())) {
            $new_sale->geolocate_without_saving();
            $new_sale->create_or_update_statistic_same_address_start();
            $new_sale->create_or_update_statistic_address_start();
            $new_sale->load_statistic();
            if ($new_sale->save()) {

                return $this->redirect(['analize-one', 'id' => $new_sale->id]);
            } else {
                var_dump($new_sale->errors);
                return $this->render('create', [
                    'model' => $new_sale,
                ]);
            }
        } else return $this->render('create', [
            'model' => $new_sale,
        ]);

    }

    /* public function actionAnalizeOne($id)
     {
         $sales = Sale::findOne($id);

         $this->render('analize', [
             'sales' => $sales,
         ]);

     }*/

    public
    function actionAnalize()
    {
        $sale = Sale::find()
            ->where(['id_sources' => 9])
            ->all();
        //   var_dump($sale);

        $this->render('analize', [
            'sale_analized' => $sale,
        ]);

    }


    /**
     * Updates an existing Sale model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public
    function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public
    function actionFill()
    {
        $sales = Sale::find()
            ->where(['locality' => null])
            ->all();
        echo count($sales);

        foreach ($sales as $sale) {
            $salehistory = SaleHistory::findOne($sale->id);
            $sale_to_change = Sale::findOne($sale->id);
            $sale_to_change->locality = $salehistory->locality;
            //  $sale_to_change->district = $salehistory->district;
            $sale_to_change->save();
        }
    }

    public
    function actionShowViews()
    {
        $sales = Sale::find()
            ->where(['rooms_count' => 1])
            ->andwhere(['<>', 'count_of_views', 0])
            ->orderBy('original_date DESC')
            ->limit(50)
            ->all();

        return $this->render('views', [
            'sales' => $sales,
        ]);

    }

    /**
     * Deletes an existing Sale model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public
    function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public
    function actionSetModerated($id)
    {
        $this->findModel($id)->setModerated();

        // return $this->redirect(['index']);
    }

    public
    function actionCopyTags($id_from, $id_to)
    {
        $sale_from = RealTags::find()->where(['sale_id' => $id_from])->one();
        if ($sale_from) {
            $sale_to = RealTags::find()->where(['sale_id' => $id_to])->one();
            if (!$sale_to) {
                $sale_to = new RealTags();
                $session = Yii::$app->session;
                $sale_to->user_id = $session->get('user_id');
                $sale_to->sale_id = $id_to;
            }

            $sale_to->tags_id = $sale_from->tags_id;
            $sale = Sale::findOne($id_to);
            $sale->moderated = 1;
            if (!$sale_to->save()) my_var_dump($sale_to->getErrors());
            if (!$sale->save()) my_var_dump($sale->getErrors());
        } // else echo "y данного нет tags";

    }

    /**
     * Finds the Sale model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Sale the loaded model
     * @return Sale the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = Sale::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public
    function actionErrorGeocodetion($id, $geocodated)
    {
        $sale = Sale::findOne($id);
        if ($sale) {
            $sale->id_address = null;
            $sale->geocodated = $geocodated;
            $sale->save();

        }
    }

    public
    function actionAddToFavourites($id_item)
    {
        $session = Yii::$app->session;
        $list_of_ids = $session->get('list_of_ids');
        if (!empty($list_of_ids)) {

            $array = explode(",", $list_of_ids);
            array_push($array, $id_item);
            $list_of_ids = implode(",", array_unique($array, SORT_STRING));
        } else {
            echo " hello";
            $list_of_ids = $id_item;
        }


        $session->set('list_of_ids', $list_of_ids);

    }

    public
    function actionSetSold($id)
    {
        $sale = Sale::findOne($id);
        $sale->set_sold();
    }

    public
    function actionMdbtest()
    {
        return $this->render('test');
    }

}
