<?php

namespace app\controllers;

use app\models\Addresses;
use app\models\Agents;
use app\models\Orders;
use app\models\RealTags;
use app\models\Sale;
use app\models\SaleFilters;
use app\models\SaleFiltersSearch;
use app\models\SaleSearch;
use app\models\SaleLists;
use phpDocumentor\Reflection\Types\Self_;
use yii\db\Expression;
use Yii;

class WebController extends \yii\web\Controller
{
    public static function setPrefixies($prefix = 'Velikiy_Novgorod')
    {
        Sale::setTablePrefix($prefix);
        SaleLists::setTablePrefix($prefix);
        SaleFilters::setTablePrefix($prefix);
        Addresses::setTablePrefix($prefix);
        RealTags::setTablePrefix($prefix);
        Agents::setTablePrefix($prefix);
    }


    public function beforeAction($action)
    {
        WebController::setPrefixies();
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionSearchBy($id = null)
    {
        $this->layout = 'lp';
        $session = Yii::$app->session;
        if (!$id) {
            $salefilter = SaleFilters::find()->where(['type' => SaleFilters::PUBLIC_TYPE])->orderBy( new Expression( 'rand()'))->one();
           // $salefilter = SaleFilters::findOne(152);
        } else {
            $salefilter = SaleFilters::findOne($id);
        }

        $searchModel = new SaleSearch();
        // если проставлен поиск по типу
         $dataProvider = $searchModel->web_search($salefilter);

     //   return $this->render('@app/views/my-debug/debug');

        return $this->render('lp', [
            'salefilter' => $salefilter,
           'dataProvider' => $dataProvider

        ]);
   }
//public function actionSearchBy($id = null)
//    {
//        $this->layout = 'lp';
//        $session = Yii::$app->session;
//
//        $searchModel = new SaleSearch();
//        //  $salelist = SaleLists::find()->where(['id' => $id])->one();
//        $salelist = SaleLists::find()->where(['>', 'id', 0])->andwhere(['type' => 2])->andfilterWhere(['id' => $id])->orderBy(new Expression('rand()'))->one();
//        if (Yii::$app->request->post('phone')) {
//            $order = new Orders();
//            $order->load(Yii::$app->request->post());
//            if ($order->save()) {
//                $order->send();
//                $session->setFlash('order_get', true);
//            }//  else my_var_dump($order->getErrors());
//        }
//
//        $data = $searchModel->search_sale_list($salelist);
//
//
//        return $this->render('lp', [
//            'salelist' => $salelist,
//            'data' => $data['data'],
//            'pages' => $data ['pages']
//
//        ]);
//   }
// public function actionSearchBy($id = null)
//    {
//        $session = Yii::$app->session;
//        $this->layout = 'web';
//        $searchModel = new SaleSearch();
//        //  $salelist = SaleLists::find()->where(['id' => $id])->one();
//        $salelist = SaleLists::find()->where(['>', 'id', 0])->andwhere(['type' => 2])->andfilterWhere(['id' => $id])->orderBy(new Expression('rand()'))->one();
//        if (Yii::$app->request->post('phone')) {
//            $order = new Orders();
//            $order->load(Yii::$app->request->post());
//            if ($order->save()) {
//                $order->send();
//                $session->setFlash('order_get', true);
//            }//  else my_var_dump($order->getErrors());
//        }
//
//        $data = $searchModel->search_sale_list($salelist);
//
//
//        return $this->render('search-by', [
//            'salelist' => $salelist,
//            'data' => $data['data'],
//            'pages' => $data ['pages']
//
//        ]);
//    }

    public
    function actionIndex($id = 0)
    {

        $this->layout = 'web';

        return $this->render('index');
    }

    public
    function actionTest($id = 0)
    {

        $this->layout = 'web';

        return $this->render('testing-grid');
    }

    function actionOrders($id = 0)
    {

        $this->layout = 'web';

        return $this->render('orders');
    }

    public
    function actionMissedSales()
    {
        $session = Yii::$app->session;
        //  my_var_dump(Yii::$app->request->post());

        if (Yii::$app->request->post('phone')) {
            $order = new Orders();
            $order->load(Yii::$app->request->post());
            if ($order->save()) $session->setFlash('order_get', true); else my_var_dump($order->getErrors());
        }

        $this->layout = 'web';
        $searchModel = new SaleSearch();
        $salelist = SaleLists::find()->where(['>', 'id', 0])->andWhere(['type' => 6])->orderBy(new Expression('rand()'))->one();
        $data = $searchModel->search_sale_list($salelist);


        return $this->render('missed-sales', [
            'salelist' => $salelist,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }

    public
    function actionClients()
    {
        $this->layout = 'web';
        $clientsSearch1 = new SaleFiltersSearch();
        $clients_1 = $clientsSearch1->getClients(['clients', 'rooms_count' => [1]]);
        $clientsSearch2 = new SaleFiltersSearch();
        $clients_2 = $clientsSearch2->getClients(['clients', 'rooms_count' => [2]]);
        $clientsSearch3 = new SaleFiltersSearch();
        $clients_3 = $clientsSearch3->getClients(['clients', 'rooms_count' => [3]]);
        $clientsSearch30 = new SaleFiltersSearch();
        $clients_30 = $clientsSearch30->getClients(['clients', 'rooms_count' => [30]]);
        return $this->render('clients', [
            'clients_1' => $clients_1,
            'clients_2' => $clients_2,
            'clients_3' => $clients_3,
            'clients_30' => $clients_30
        ]);
    }

    public
    function actionContacts()
    {
        $this->layout = 'web';
        return $this->render('contacts');
    }

    public
    function actionLandingPage($id  = null)
    {
        $this->layout = 'lp';
        $session = Yii::$app->session;

        $searchModel = new SaleSearch();
        //  $salelist = SaleLists::find()->where(['id' => $id])->one();
        $salelist = SaleLists::find()->where(['>', 'id', 0])->andwhere(['type' => 2])->andfilterWhere(['id' => $id])->orderBy(new Expression('rand()'))->one();
        if (Yii::$app->request->post('phone')) {
            $order = new Orders();
            $order->load(Yii::$app->request->post());
            if ($order->save()) {
                $order->send();
                $session->setFlash('order_get', true);
            }//  else my_var_dump($order->getErrors());
        }

        $data = $searchModel->search_sale_list($salelist);


        return $this->render('lp', [
            'salelist' => $salelist,
            'data' => $data['data'],
            'pages' => $data ['pages']

        ]);

    }

    public
    function actionAdvices($q = '')
    {
        $session = Yii::$app->session;

        if (Yii::$app->request->post('phone')) {
            $order = new Orders();
            $order->load(Yii::$app->request->post());
            if ($order->save()) {
                $order->send();
                $session->setFlash('order_get', true);
            }//  else my_var_dump($order->getErrors());
        }

        $this->layout = 'web';
        if (!empty($q)) return $this->render("advices/" . $q);
        else return $this->render('advices');
    }

    public function actionSetContactFormShown($q = '')
    {
        if ($q == 'ms') {
            $session = Yii::$app->session;
            $session->set('contact-form-missed-sale-notice', true);
        }
        if ($q == 'sb') {
            $session = Yii::$app->session;
            $session->set('contact-form-search-by-notice', true);
        }


    }

}
