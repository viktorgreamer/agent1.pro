<?php

namespace app\controllers;

use app\components\MdbActiveSelect;
use app\models\Addresses;
use app\models\Sale;
use app\models\SalefiltersModels\SaleFiltersOnControl;
use Yii;
use app\models\SaleFilters;
use app\models\SaleFiltersSearch;
use yii\helpers\Html;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use app\components\Mdb;
use app\models\MyArrayHelpers;
/**
 * SaleFiltersController implements the CRUD actions for SaleFilters model.
 * @var $salefilter SaleFilters;
 */
class SaleFiltersController extends Controller
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
                    //'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SaleFilters models.
     * @return mixed
     */

    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

    public function actionToggleRelevantedIds($salefilter_id, $similar_id)
    {
        $filters_add_to = SaleFilters::findOne($salefilter_id);
        if ($filters_add_to->relevanted_ids) {
            info('уже есть связанные ids');
            $filters_existed = explode(",", $filters_add_to->relevanted_ids);
            $filters_existed = MyArrayHelpers::AddOrDelete($filters_existed, $similar_id);

            $filters_add_to->relevanted_ids = implode(",", $filters_existed);
        } else  {
            info('НЕТ связанные ids');
            $filters_add_to->relevanted_ids = $similar_id;
        }
        echo $filters_add_to->relevanted_ids;

        if (!$filters_add_to->save()) my_var_dump($filters_add_to->getErrors());

        return $this->render('debug');


    }

    public function actionSearchBy()
    {
        $session = Yii::$app->session;

        $searchModel = new SaleFiltersSearch();
        $salefilter = new SaleFilters();
        $salefilter->load(Yii::$app->request->get());
        $salefilter->user_id = $session->get('user_id');
        if ($salefilter->id == 0) {
            $salefilter = SaleFilters::findOne(['user_id' => $session->get('user_id')]);
        }

        //$dataProvider = SaleSearch::search($salefilter);
        $data = $searchModel->search($salefilter);


        return $this->render('index', [
            'salefilter' => $salefilter,
            //'dataProvider' => $dataProvider
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);

    }



    public function actionIndex()
    {

        $searchModel = new SaleFiltersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionSaveCurrentSalefilter($name = '',$type = 1)
    {

        if ($name == '') $name = "Фильтр_" . date('Y-m-d H:i:s', time());
        $session = Yii::$app->session;
        $salefilter = $session->get('current_filter');

        $is_existed_salefilter = SaleFilters::find()->where(['user_id' => $session['user_id']])->andWhere(['name' => $name])->one();
        if ($is_existed_salefilter) {
            //  my_var_dump($is_existed_salefilter);
            //  my_var_dump($salefilter);
            $black_list_id = $is_existed_salefilter->black_list_id;
            $white_list_id = $is_existed_salefilter->white_list_id;
            $similar_white_list_id = $is_existed_salefilter->similar_white_list_id;
            $similar_black_list_id = $is_existed_salefilter->similar_black_list_id;
            $minus_id_addresses = $is_existed_salefilter->minus_id_addresses;
            $user_id = $is_existed_salefilter->user_id;
            // обновляем все атрибуты
            $is_existed_salefilter->copyAttributes($salefilter);
            // но сохраняем все исключения

            //  $is_existed_salefilter->price_down = 1100000;
            $is_existed_salefilter->user_id =  $user_id;
            $is_existed_salefilter->black_list_id = $black_list_id;
            $is_existed_salefilter->white_list_id = $white_list_id;
            $is_existed_salefilter->similar_white_list_id = $similar_white_list_id;
            $is_existed_salefilter->similar_black_list_id = $similar_black_list_id;
            $is_existed_salefilter->minus_id_addresses = $minus_id_addresses;

            if (!$is_existed_salefilter->save(false)) return my_var_dump($is_existed_salefilter->getErrors());
            else {
                Mdb::Alert(" Успешно обновили Фильтр: " . $is_existed_salefilter->name, 'success');
                return Html::a("Поиск по фильтру '".span($is_existed_salefilter->name, 'success')."'", ['sale/search-by-filter', 'id' => $is_existed_salefilter->id]);
                 }
        } else {
            $salefilter->name = $name;
            $salefilter->type = $type;
            if (!$salefilter->save(false)) return my_var_dump($salefilter->getErrors());
            else {
                Mdb::Alert(" Успешно Сохранили Фильтр: " . $salefilter->name, 'success');
                return Html::a("Поиск по фильтру '".span($salefilter->name, 'success')."'", ['sale/search-by-filter', 'id' => $salefilter->id]);
            }
        }
        // return $this->render('@app/views/console/processing');

    }

    public function actionIsExistedNameSalefilter($name = '')
    {
        $session = Yii::$app->session;
        $is_existed_salefilter = SaleFilters::find()->where(['user_id' => $session['user_id']])->andWhere(['name' => $name])->one();
        if ($is_existed_salefilter) return "Фильтр с именем " . $name . " существует. ";
        else return "NO";
    }

    public function actionShowLists($type = 0)
    {
        $type = $_GET['type'];

        return $this->renderAjax('_show-lists', ['salefilters' => SaleFilters::getMyFiltersAsArray($type)]);
    }





    public function actionAddItemAjax()
    {

        $session = Yii::$app->session;
        $id = Yii::$app->request->get('id');
        $id_item = Yii::$app->request->get('id_item');

        $salefilter = SaleFilters::findOne($id); // выюираем id фильтра
        $salefilter->time = time();
        // $salefilter->addTo('white', $id_item); // добавляет и сохраняет

        $salefilter->add_to_white_list($id_item); // добавляет и сохраняет
        $sale = Sale::findOne($id_item);
        return " добавили в белый список " . $sale->address;

    }

    public function actionOnControl()
    {
        $price = Yii::$app->request->get('price');
        $id_similar = Yii::$app->request->get('id_item');;
        $id_salefilter = Yii::$app->request->get('id_salefilter');
       $salefilter = SaleFilters::findOne($id_salefilter);

        return $salefilter->OnControl($id_similar, $price);


    }

    public function actionAddItemToCheck()
    {

        $session = Yii::$app->session;
        $id = Yii::$app->request->get('id');
        $id_item = Yii::$app->request->get('id_item');

        $salefilter = SaleFilters::findOne($id); // выюираем id фильтра
        $salefilter->add_to_check_list($id_item); // добавляет и сохраняет
        $sale = Sale::findOne($id_item);
        return " добавили в контрольный список " . $sale->address;

    }

    public function actionDeleteItemFromWhiteListAjax()
    {

        $session = Yii::$app->session;
        $id = Yii::$app->request->get('id');
        $id_item = Yii::$app->request->get('id_item');

        $salefilter = SaleFilters::findOne($id); // выюираем id фильтра
        $new_Array = explode(",", $salefilter->white_list_id);
        unset($new_Array[array_search($id_item, $new_Array)]);
        $salefilter->white_list_id = implode(",", $new_Array);
        $salefilter->save();

    }

    public function actionDeleteItemFromBlackListAjax()
    {

        $session = Yii::$app->session;
        $id = Yii::$app->request->get('id');
        $id_item = Yii::$app->request->get('id_item');

        $salefilter = SaleFilters::findOne($id); // выюираем id фильтра
        $new_Array = explode(",", $salefilter->black_list_id);
        unset($new_Array[array_search($id_item, $new_Array)]);
        $salefilter->black_list_id = implode(",", $new_Array);
        $salefilter->save();

    }


    /**
     * Displays a single SaleFilters model.
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
     * Creates a new SaleFilters model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SaleFilters();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SaleFilters model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->afterFind();
        $model->load(Yii::$app->request->post());



        if (!$model->save(false)) my_var_dump($model->getErrors());
        return $this->render('update', [
            'salefilter' => $model,
        ]);


    }

    public function actionSearch()

    {

        $searchModel = new SaleFiltersSearch();
        $dataProvider = $searchModel->my_search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Deletes an existing SaleFilters model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionLoadPhoto($id)
    {
       $filter = SaleFilters::findOne($id);
       $filter->LoadPhotoToLocal();

        return $this->render('debug');
    }



    /**
     * Finds the SaleFilters model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleFilters the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleFilters::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function actionDownloadPhotoAndResize($id = 0)
    {
        $salefilter = SaleFilters::findOne($id);
       $salefilter->LoadPhotoToLocal();

    }

}
