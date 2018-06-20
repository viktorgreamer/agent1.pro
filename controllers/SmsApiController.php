<?php

namespace app\controllers;

use Yii;
use app\models\SmsApi;
use app\models\SmsApiSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\SmsApiBan;

/**
 * SmsApiController implements the CRUD actions for SmsApi model.
 */
class SmsApiController extends Controller
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
     * Lists all SmsApi models.
     * @return mixed
     */


    public function actionIndex($id = 0)
    {
        $searchModel = new SmsApiSearch();
        $session = Yii::$app->session;
        $sms_api_get_id = new SmsApi();
        if ($id == 0) {
            $sms_api_get_id->load(Yii::$app->request->get());
        } else  $sms_api_get_id = SmsApi::findOne(['id_list' => $id]);


        if ($sms_api_get_id->id_list == 0) {
            $sms_api_get_id = SmsApi::findOne(['user_id' => $session->get('user_id')]);

        }

        $sms_api = SmsApi::find()
            ->where(['id_list' => $sms_api_get_id->id_list])
            ->andFilterWhere(['status' => $sms_api_get_id->status])
            ->one();

        if ($sms_api == Null) {
            $sms_api = SmsApi::find()
                ->where(['id_list' => $sms_api_get_id->id_list])
                ->one();

        }

        // поиск id_list определенного статуса
        $data = $searchModel->search($sms_api->id_list, $sms_api->status);

        return $this->render('index', [
            'sms_api' => $sms_api,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }

    public function actionEdit($id = 0)
    {
        $searchModel = new SmsApiSearch();
        $session = Yii::$app->session;
        $sms_api_get_id = new SmsApi();
        $sms_api_get_id->load(Yii::$app->request->get());
        $text_sms = $sms_api_get_id->text_sms;
        echo $sms_api_get_id->status;

        if ($sms_api_get_id->id_list == '') {
            $sms_api_get_id = SmsApi::findOne(['user_id' => $session->get('user_id')]);

        }

        $sms_api = SmsApi::find()
            ->where(['id_list' => $sms_api_get_id->id_list])
            ->andFilterWhere(['status' => $sms_api_get_id->status])
            ->one();

        if ($sms_api == Null) {
            $sms_api = SmsApi::find()
                ->where(['id_list' => $sms_api_get_id->id_list])
                ->one();

        }


        $data = $searchModel->search($sms_api->id_list, $sms_api->status);

        return $this->render('edit', [
            'text_sms' => $text_sms,
            'sms_api' => $sms_api,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }


    public function actionSave($id_list, $dot_text_sms, $status)
    {
// делаем выбор отредактированных смс и вносим с них изменения
        $sms_apis = SmsApi::find()->where(['id_list' => $id_list])->andWhere(['status' => $status])->all();
        foreach ($sms_apis as $sms_api) {
            $text_sms = str_replace("{name}", $sms_api['person'], $dot_text_sms);
            $text_sms = str_replace("{price}", $sms_api['price'], $text_sms);
            $text_sms = str_replace("{address}", $sms_api['address'], $text_sms);
            $sms_api_id_edit = SmsApi::findOne(['id' => $sms_api['id']]);
            $sms_api_id_edit->text_sms = $text_sms;
            $sms_api_id_edit->status = 1;
            $sms_api_id_edit->save();
        }

    }

    public function actionSend($id_list)
    {


        $url = "https://semysms.net/api/3/sms_more.php"; //Адрес url для отправки СМС
        $device = '62936';  // Код вашего устройства
        $token = '96618ac1ac70924841194a9821a948fd';  // Ваш токен (секретны


        $params = array('token' => $token);

        $sms_apis = SmsApi::find()->where(['id_list' => $id_list])->andFilterWhere(['status' => 1])->all();
        foreach ($sms_apis as $sms_api) {

            $sms_api_change_status = SmsApi::findOne($sms_api['id']);
            $sms_api_change_status->status = 3;
            $sms_api_change_status->save();


            $array_of_sms = explode("&", $sms_api['text_sms']);
            foreach ($array_of_sms as $sms) {
                $params['data'][] = array(
                    'my_id' => '',
                    'device' => $device,
                    'phone' => $sms_api['phone'],
                    'msg' => $sms);

                $array_sms_test[] = [
                    'phone' => $sms_api['phone'],
                    'msg' => $sms
            ];

            }


        }


        $params = json_encode($params);


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params))
        );
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        $result = curl_exec($curl);


        curl_close($curl);


        return $this->render('send-sms',
            ['array_sms_test' => $array_sms_test,
                'device' => $device,
                'token' => $token,
                'result' => $result
            ]);

    }

    public function actionMoveToSmsApiBanList($phone)
    {
        $session = Yii::$app->session;
        $sms_ban = New SmsApiBan();
        $sms_ban->phone = $phone;
        $sms_ban->user_id = $session->get('user_id');
        $sms_ban->save();
    }


    public
    function actionDeleteDublicate($status)
    {
        $searchModel = new SmsApiSearch();
        $session = Yii::$app->session;
        $sms_api_get_id = new SmsApi();
        $sms_api_get_id->load(Yii::$app->request->get());


        if ($sms_api_get_id->id_list == '') {
            $sms_api_get_id = SmsApi::findOne(['user_id' => $session->get('user_id')]);

        }
        $sms_api = SmsApi::find()->where(['id_list' => $sms_api_get_id->id_list])->one();


        $data = $searchModel->search_for_edit($sms_api, $status);

        /* echo "<pre>";
         var_dump($data['data']);
         echo "</pre>";*/
        $data_phones = $data['data'];

        foreach ($data_phones as $phone) {
            if (SmsApi::find()->where(['phone' => $phone['phone']])->count() > 1) {
                $delete_item_sms_api = SmsApi::findone(['phone' => $phone['phone']]);
                $delete_item_sms_api->delete();
                $count_of_dubbles++;
            }
        }

        echo "количество дубликатов" . $count_of_dubbles;
        return $this->render('edit', [
            'sms_api' => $sms_api,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }

    public
    function actionDeleteDublicateAll()
    {
        $searchModel = new SmsApiSearch();
        $session = Yii::$app->session;
        $sms_api_get_id = new SmsApi();
        $sms_api_get_id->load(Yii::$app->request->get());


        if ($sms_api_get_id->id_list == '') {
            $sms_api_get_id = SmsApi::findOne(['user_id' => $session->get('user_id')]);

        }
        $sms_api = SmsApi::find()->where(['id_list' => $sms_api_get_id->id_list])->one();


        $data = $searchModel->search_without_status($sms_api->id_list);

        /* echo "<pre>";
         var_dump($data['data']);
         echo "</pre>";*/
        $data_phones = $data['data'];

        foreach ($data_phones as $phone) {
            if (SmsApi::find()->where(['phone' => $phone['phone']])->count() > 1) {
                $delete_item_sms_api = SmsApi::findone(['phone' => $phone['phone']]);
                $delete_item_sms_api->delete();
                $count_of_dubbles++;
            }
        }

        echo "количество дубликатов" . $count_of_dubbles;
        return $this->render('edit', [
            'sms_api' => $sms_api,
            'data' => $data['data'],
            'pages' => $data ['pages']
        ]);


    }

    public
    function actionDeleteOne($id_list, $id)
    {

        $sms_api = SmsApi::find()
            ->where(['id' => $id])
            ->andFilterWhere(['id_list' => $id_list])
            ->one();

        $sms_api->delete();


    }

    public
    function actionDelayOne($id)
    {

        $sms_api = SmsApi::find()->where(['id' => $id])->one();
        $sms_api->status = 2;
        $sms_api->save();


    }


    public
    function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }


    public
    function actionCreate()
    {
        $model = new SmsApi();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SmsApi model.
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

    /**
     * Deletes an existing SmsApi model.
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

    /**
     * Finds the SmsApi model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SmsApi the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected
    function findModel($id)
    {
        if (($model = SmsApi::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
