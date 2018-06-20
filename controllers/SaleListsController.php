<?php

namespace app\controllers;

use app\models\Addresses;
use app\models\MyArrayHelpers;
use app\models\Sale;
use app\models\SaleSearch;
use app\models\SmsApi;
use app\models\SmsApiBan;
use Yii;
use app\models\SaleLists;
use app\models\SaleListsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * SaleListsController implements the CRUD actions for SaleLists model.
 */
class SaleListsController extends Controller
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
                    //  'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all SaleLists models.
     * @return mixed
     */
    public function actionDeleteIdAddress($id, $id_address) {
        $salelist = SaleLists::findOne($id);
        if ($salelist->list_of_ids)  {
            $lists_of_ids = explode(",",$salelist->list_of_ids);
            $sales = Sale::find()->select('id,id_address')->where(['in', 'id',$lists_of_ids])->all();
            $new_ids = [];
            $n = 0;
            foreach ($sales as $sale) {
                if ($sale->id_address != $id_address)  array_push($new_ids, $sale->id);
                else $n++;
            }
            if (!empty($new_ids)) $salelist->list_of_ids = implode(',', $new_ids);
            if (!$salelist->save()) my_var_dump($salelist->getErrors());
        }
           if ($n > 0) return "успешно удалили ". $n ." записей";

    }
    public function actionSearchBy($id = 0)
    {

        $session = Yii::$app->session;

        $salelist_get_id = new SaleLists();
        $salelist_get_id->load(Yii::$app->request->get());
        /*  echo "<pre>";
          var_dump($salelist_get_id);
          echo "</pre>";*/

        $searchModel = new SaleSearch();
        if ($id != 0) $salelist_get_id->id = $id;
        // первый раз заходим на страницу то get параметр не определен выподим первый попавшийся
        if ($salelist_get_id->id == 0) {
            $salelist_get_id = SaleLists::findOne(['user_id' => $session->get('user_id')]);
        }
        $salelist = SaleLists::find()->where(['id' => $salelist_get_id->id])->one();

        $data = $searchModel->search_sale_list($salelist);


        return $this->render('search-by', [
            'salelist' => $salelist,
            'data' => $data['data'],
            'pages' => $data ['pages']

        ]);


    }

    /**
     * Lists all SaleLists models.
     * @return mixed
     */
    /*public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => SaleLists::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }*/
    public function actionIndex()
    {
        $searchModel = new SaleListsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionEditBeforeSent()
    {

        $session = Yii::$app->session;

        $salelist_get_id = new SaleLists();
        $salelist_get_id->load(Yii::$app->request->get());
        /*  echo "<pre>";
          var_dump($salelist_get_id);
          echo "</pre>";*/

        $searchModel = new SaleSearch();


        if ($salelist_get_id->id == 0) {
            $salelist_get_id = SaleLists::findOne(['user_id' => $session->get('user_id')]);
        }
        $salelist = SaleLists::find()->where(['id' => $salelist_get_id->id])->one();


        $data = $searchModel->search_sale_list($salelist);


        return $this->render('index', [
            'salelist' => $salelist,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }


    public function actionDeleteItem($id, $id_item)
    {

        $session = Yii::$app->session;

        $salelist = SaleLists::findOne($id); // выюираем id списка
        $new_Array = explode(",", $salelist->list_of_ids);
        unset($new_Array[array_search($id_item, $new_Array)]);

        $salelist->list_of_ids = implode(",", $new_Array);
        $salelist->timestamp = time();
        $salelist->save();

    }

    public function actionOkItem($id, $id_item)
    {

        $session = Yii::$app->session;

        $salelist = SaleLists::findOne($id); // выюираем id списка
        $new_Array = explode(",", $salelist->ids_ok);

        // если в списке что-то есть
        if ($salelist->ids_ok != '') {
            //  echo " если в списке что-то есть";
            // получаем список уже имеющихся tags
            $new_Array = explode(',', $salelist->ids_ok);
            // если там данный tag есть то удаляем его если нет то добавляем
            if (in_array($id_item, $new_Array)) unset($new_Array[array_search($id_item, $new_Array)]);
            else  array_push($new_Array, $id_item);
            // переводим массив в список
            if (count($new_Array) == 0) $salelist->ids_ok = ''; else $salelist->ids_ok = implode(",", $new_Array);

        } else {
            // echo " список был пустой";
            $salelist->ids_ok = $id_item;
        } // если ничего не было, то сразу добавляем
       ;
      
        $salelist->timestamp = time();
        $salelist->save();

    }

    public function actionBanItem($id, $id_item)
    {

        $session = Yii::$app->session;

        $salelist = SaleLists::findOne($id); // выюираем id списка
        $new_Array = explode(",", $salelist->ids_ban);
        // если в списке что-то есть
        if ($salelist->ids_ban != '') {
            //  echo " если в списке что-то есть";
            // получаем список уже имеющихся tags
            $new_Array = explode(',', $salelist->ids_ban);
            // если там данный tag есть то удаляем его если нет то добавляем
            if (in_array($id_item, $new_Array)) unset($new_Array[array_search($id_item, $new_Array)]);
            else  array_push($new_Array, $id_item);
            // переводим массив в список
            if (count($new_Array) == 0) $salelist->ids_ban = ''; else $salelist->ids_ban = implode(",", $new_Array);

        } else {
            // echo " список был пустой";
            $salelist->ids_ban = $id_item;
        } // если ничего не было, то сразу добавляем
        ;
        $salelist->timestamp = time();
        $salelist->save();
    }

    public function actionDeleteItemAjax()
    {

        $session = Yii::$app->session;
        $id = Yii::$app->request->get('id');
        $id_item = Yii::$app->request->get('id_item');

        $salelist = SaleLists::findOne($id); // выюираем id списка
        $new_Array = explode(",", $salelist->list_of_ids);
        unset($new_Array[array_search($id_item, $new_Array)]);

        $salelist->list_of_ids = implode(",", $new_Array);
        $salelist->timestamp = time();
        $salelist->save();

    }

    /**
     * Displays a single SaleLists model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionExportAjax($id)
    {
        $session = Yii::$app->session;

        $salelist = SaleLists::findOne($id); // выюираем id списка

        $sale = Sale::find()// загружаем полностью все объявления с этими id в модель Sale
        ->select(['address', 'phone1', 'price', 'person', 'id', 'grossarea', 'rooms_count'])
            ->where(['in', 'id', explode(",", $salelist->list_of_ids)])
            ->all();
        $max_id_sms_api_list = SmsApi::find()->max('id_list');

        foreach ($sale as $item) { // все записи из модеои sale  перегоняем в модель smsapi
            // если есть в ban листе то не добавляем этот телефон в экспорт
            if (SmsApiBan::find()->where(['user_id' => $session->get('user_id')])
                ->andFilterWhere(['phone' => $item->phone1])
                ->exists()
            ) continue;
            $sms_api_item = new SmsApi();
            $sms_api_item->id_sale = $item->id;
            $sms_api_item->rooms_count = $item->rooms_count;
            $sms_api_item->grossarea = $item->grossarea;
            $sms_api_item->address = $item->address;
            $sms_api_item->phone = $item->phone1;
            $sms_api_item->person = $item->person;
            $sms_api_item->price = $item->price;
            $sms_api_item->user_id = $session->get('user_id');
            $sms_api_item->name = $salelist->name;
            $sms_api_item->id_list = $max_id_sms_api_list + 1;
            $sms_api_item->save();

        }

        return $this->render('export', [
            'sale' => $sale,

        ]);

    }

    /**
     * Creates a new SaleLists model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SaleLists();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SaleLists model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->timestamp = time();

        if ($model->load(Yii::$app->request->post())) {
            if (Yii::$app->request->post('parent_salefilter')) {
                $model->parent_salefilter = implode(",", Yii::$app->request->post('parent_salefilter'));
            } else $model->parent_salefilter = '';
            $model->regions = Yii::$app->request->post('regions');

            if ($model->save()) {
                return $this->render('update', [
                    'model' => $model,
                ]);

            } else {
                my_var_dump($model->getErrors());
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        } else return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing SaleLists model.
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
     * Finds the SaleLists model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SaleLists the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SaleLists::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionUpdateForm() {
        return $this->renderPartial('lists');
    }


    public function actionDownloadPhoto($id)
    {
        $session = Yii::$app->session;
        $user_id = $session->get('user_id');
        $salelist = SaleLists::findOne($id);
        $salelist->LoadPhotoToLocal();

        $this->render('debug');


    }

    public function actionDownloadPhotoAndResize()
    {
        $salelist = SaleLists::findOne(19);
        $sales = Sale::find()
            ->where(['in', 'id', explode(",", $salelist->list_of_ids)])
            ->limit(1)
            ->all();
        echo " всего объявлений" . count($sales);
        foreach ($sales as $sale) {
            echo " <br>";
            echo $sale->id . " list of photo <a href='" . $sale->url . "'> link </a>";
            // echo " <br>";
            $links = explode("X", $sale->images);
            foreach ($links as $link) {
                //  echo " <br>";
                // echo $link;
                $name = array_pop(explode("/", $link));
                // echo " name = " . $name . " sale address" . $sale->address;
                $dir = my_transliterate($sale->id . "_" . $sale->rooms_count . "_" . preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $sale->address));
                $is_download = false;
                //если такой директории еще не существует то создаем ее и
                if (!file_exists($dir)) {
                    echo " создаем директорию";
                    mkdir($dir);
                }
                if (!file_exists($dir . "/" . $name)) {

                    if ($name != '') {
                        $target_url = str_replace('640x480', '1280x960', $link);
                        sleep(1);
                        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0';
                        $ch = curl_init($target_url);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                        curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
                        $output = curl_exec($ch);
                        $fh = fopen($dir . "/" . $name, 'w');
                        fwrite($fh, $output);
                        if (!file_exists($dir . "/" . $name)) fwrite($fh, $output);
                        fclose($fh);
                    }
                }
            }
        }


    }

    public function actionXmlResponse($id)
    {
        $salelist = SaleLists::findOne($id);
        $salelist->ExportIrr();

        return $this->render('export-irr', [
            'salelist' => $salelist

        ]);

    }

    public function actionAddFavourites($id_list)
    {
        $session = Yii::$app->session;
        $list_of_ids = $session->get('list_of_ids');
        $salelist = SaleLists::findOne($id_list);
        $salelist->addArrayOfItem($list_of_ids);
        $session->remove('list_of_ids');

    }

    public function actionTriggerSimilarList($salelist_id, $similar_id)
    {
        $lists_add_to = SaleLists::findOne($salelist_id);
        if ($lists_add_to->similar_lists) {
            $lists_existed = explode(",", $lists_add_to->similar_lists);
            $lists_existed = MyArrayHelpers::AddOrDelete($lists_existed, $similar_id);

            $lists_add_to->similar_lists = implode(",", $lists_existed);
        } else  $lists_add_to->similar_lists = $similar_id;
        echo $lists_add_to->similar_lists;

        if (!$lists_add_to->save()) my_var_dump($lists_add_to->getErrors());


    }


}




