<?php

namespace app\controllers;

use Yii;
use app\models\Synchronization;
use app\models\SynchronizationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SynchronizationController implements the CRUD actions for Synchronization model.
 */
class SynchronizationController extends Controller
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
     * Lists all Synchronization models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SynchronizationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * метод для ручного обнуления статуса взависимости от его имени
     * @return mixed
     */

    public function actionChangeStatus($id_item, $status_name)
    {
        $sync = Synchronization::findOne($id_item);

        switch ($status_name) {
            case 'geocodated': {
                $sync->geocodated = 8;
                $sync->id_address = 0;
                $new_status =  \app\models\Geocodetion::GEOCODATED_STATUS_ARRAY[$sync->geocodated];
                break;
            }
            case 'sync': {
                $sync->sync = 2;
                $new_status =  \app\models\Sale::TYPE_OF_SYNC[$sync->sync];
                break;
            }
            case 'parsed': {
                $sync->parsed = 2;
                $new_status =  \app\models\Sale::TYPE_OF_PARSED[$sync->parsed];
                break;
            }
            case 'processed': {
                $sync->processed = \app\models\Sale::TYPE_OF_PROCCESSED[$sync->processed];
                $new_status =  "0";
                break;
            }
            case 'load_analized': {
                $sync->load_analized = \app\models\Sale::TYPE_OF_ANALIZED[$sync->load_analized];
                $new_status =  "0";
                break;
            }

        }
        $sync->save();
        return "сменили статус ".$status_name." на ".$new_status;
    }

    /**
     * Displays a single Synchronization model.
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
     * Creates a new Synchronization model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Synchronization();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Synchronization model.
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
     * Deletes an existing Synchronization model.
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
     * Finds the Synchronization model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Synchronization the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Synchronization::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
