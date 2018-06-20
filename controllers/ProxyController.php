<?php

namespace app\controllers;

use Yii;
use app\models\Proxy;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ProxyController implements the CRUD actions for Proxy model.
 */
class ProxyController extends Controller
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
     * Lists all Proxy models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Proxy::find(),
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Proxy model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionUpload()
    {
        $body = trim(Yii::$app->request->post('body'));
        if ($body) {
            $rows = explode("\n", $body);
            if ($rows) {
                foreach ($rows as $row) {
                  //  info("ROW=" . $row);
                    $options = preg_split("/:/", trim($row));
                    if ($options) {
                        if (!Proxy::find()->where(['ip' => $options[0]])->one()) {
                            $proxy = new Proxy();
                            $proxy->ip = $options[0];
                            $proxy->port = $options[1];
                            $proxy->fulladdress = $proxy->ip . ":" . $proxy->port;
                            $proxy->login = $options[2];
                            $proxy->password = $options[3];
                            $proxy->add_time = time();
                            $proxy->status = 1;
                            if (!$proxy->save()) my_var_dump($proxy->errors);
                            else $succesloadcounter++;
                        } else {
                            $existedcountd++;
                            info(" THIS PROXY IS EXISTS " . $options[0],DANGER);
                        }


                    }
                }
                if ($succesloadcounter) info(" SUCCESS LOAD " . $succesloadcounter . " PROXIES FROM " . count($rows),SUCCESS);

                else info(" NOTHING TO LOAD NEW PROXY", DANGER);
            }
        }
        return $this->render('_form_upload_proxies', compact('body'));
    }

    /**
     * Creates a new Proxy model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Proxy();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Proxy model.
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
     * Deletes an existing Proxy model.
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
     * Finds the Proxy model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Proxy the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Proxy::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
