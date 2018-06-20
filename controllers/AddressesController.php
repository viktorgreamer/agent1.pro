<?php

namespace app\controllers;

use app\models\Geocodetion;
use app\models\Renders;
use app\models\Sale;
use app\models\SaleHistory;
use Yii;
use app\models\Addresses;
use app\models\AddressesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AddressesController implements the CRUD actions for Addresses model.
 */
class AddressesController extends Controller
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'verbs' => [
//                'class' => VerbFilter::className(),
//                'actions' => [
//                    'delete' => ['POST'],
//                ],
//            ],
//        ];
//    }

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    /**
     * Lists all Addresses models.
     * @return mixed
     */
    public function actionQuickSearch($id)
    {

        $address = Yii::$app->request->post('address');
        //  echo " address".Yii::$app->request->post('address');
        $addresses = AddressesSearch::QuickSearch($address);
        if ($addresses) return $this->render('quick-search', ['addresses' => $addresses, 'id' => $id]);
        else return $this->render('quick-search', ['message' => 'ничего не найдено', 'id' => $id]);
    }

    public function actionQuickSearchPjax($id, $address)
    {

        $addresses = AddressesSearch::QuickSearch($address);
        if ($addresses) return $this->renderAjax('quick-search-view', ['addresses' => $addresses, 'id' => $id]);
        else return $this->renderAjax('quick-search-view', ['message' => 'ничего не найдено', 'id' => $id]);
    }

    public function actionSetIdAddress($id, $id_address)
    {
        $sale = Sale::findOne($id);
        $sale->id_address = $id_address;
        $sale->sync = 1;
        $sale->geocodated = 7;
        $address = Addresses::findOne($id_address);
        if ($sale->save()) return "успешно присволили адрес:<br> " . $address->address . "<br>" . $address->floorcount . " этанжн. <br>" . $address->RenderHouseType();
        else return "не удалось присвоить адрес: " . Addresses::findOne($id_address)->address;
    }

    public function actionIndex()
    {

        $searchModel = new AddressesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionSearch($street, $house, $hull, $localilty)
    {

        $founded_id_address = new Addresses();
        $founded_id_address->find_by_like($street, $house, $hull, $localilty);


        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Addresses model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Addresses model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Addresses();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Addresses model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
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


    /**
     * Deletes an existing Addresses model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {

        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Addresses model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Addresses the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {

        if (($model = Addresses::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionSetYearAndFixIt()
    {
        $id_address = Yii::$app->request->get('id_address');
        $year = Yii::$app->request->get('year');
//        $prefix = 'test';
//
//        Addresses::setTablePrefix($prefix);

        $address = Addresses::findOne($id_address);
        $address->year = $year;
        $address->fullfilled = 1;
        $address->status = 1;
        if (!$address->save()) var_dump($address->errors);

    }

    public function actionSetFloorcountAndFixIt()
    {
        $id_address = Yii::$app->request->get('id_address');
        $floorcount = Yii::$app->request->get('floorcount');
//        $prefix = 'test';
//
//        Addresses::setTablePrefix($prefix);

        $address = Addresses::findOne($id_address);
        $address->floorcount = $floorcount;
        $address->fullfilled = 1;
        $address->status = 1;
        if (!$address->save()) var_dump($address->errors);

    }

    public function actionSetHousetypeAndFixIt()
    {
        $id_address = Yii::$app->request->get('id_address');
        $house_type = Yii::$app->request->get('house_type');
//        $prefix = 'test';
//
//        Addresses::setTablePrefix($prefix);

        $address = Addresses::findOne($id_address);
        $address->house_type = $house_type;
        $address->fullfilled = 1;
        $address->status = 1;
        if (!$address->save()) var_dump($address->errors);

    }

    public function actionSetHullAndFixIt()
    {
        $id_address = Yii::$app->request->get('id_address');
        $hull = Yii::$app->request->get('hull');
//        $prefix = 'test';
//
//        Addresses::setTablePrefix($prefix);

        $address = Addresses::findOne($id_address);
        $address->hull = $hull;
        $address->precision_yandex = 'exact';
        $address->fullfilled = 1;
        $address->status = 1;
        if (!$address->save()) var_dump($address->errors);

    }

    public function actionFix($id)
    {
        $address = Addresses::findOne($id);
        $address->status = 1;
        if (!$address->save()) var_dump($address->errors);

    }

    public function actionNotLiving($id)
    {
        $address = Addresses::findOne($id);
        $address->status = 2;

        if (!$address->save()) var_dump($address->errors);

    }

    public function actionReset()
    {
        $sales = SaleHistory::find()
            //->where(['in', 'precision_yandex', ['exact', 'street', 'near']])

            ->andwhere(['<', 'coords_x', 50])
            //  ->limit(50)
            ->all();
        echo count($sales);
        foreach ($sales as $sale) {

            if ($sale->id_address) {
                $address = Addresses::findOne($sale->id_address);
                $sale->coords_x = $address->coords_x;
                $sale->coords_y = $address->coords_y;
                if (!$sale->save()) my_var_dump($sale->errors);
            }


            // $geocodation = New Geocodetion();
            // $geocodation-


        }
        return $this->render('debug');
    }

    public function actionGetText()
    {
        //if (Yii::$app->request->isAjax) {
        $address = Yii::$app->request->post('address');
        if ($address) {
            $addresses = AddressesSearch::QuickSearch($address);
        }
        return $this->render('_form1', [
            'addresses' => $addresses,
        ]);
        //}
        //throw new \yii\web\BadRequestHttpException(Yii::t('app', 'Bad request!'));
    }
}
