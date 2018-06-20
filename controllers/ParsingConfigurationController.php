<?php

namespace app\controllers;

use Yii;
use app\models\ParsingConfiguration;
use app\models\ParsingConfigurationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Html;

/**
 * ParsingConfigurationController implements the CRUD actions for ParsingConfiguration model.
 */
class ParsingConfigurationController extends Controller
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
     * Lists all ParsingConfiguration models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ParsingConfigurationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ParsingConfiguration model.
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
     * Creates a new ParsingConfiguration model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ParsingConfiguration();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAutoGeneration()
    {
        $aliases = [
            1 => ['velnovgorod', 'pskov'],
            2 => ['velikiy_novgorod', 'pskov'],
            3 => ['velikiy_novgorod', 'pskov'],
            5 => ['novgorod', 'pskov'],
        ];
        $existed = ParsingConfiguration::find()->where(['module_id' => 1])->all();
        info(count($existed));
        foreach ($existed as $item) {
            $model = new ParsingConfiguration();
            $model->id_sources = $item->id_sources;
            $model->module_id = 2;
            $model->start_link = preg_replace("/" . $aliases[$item->id_sources][0] . "/", $aliases[$item->id_sources][1], $item->start_link);
            $model->non_start_link = preg_replace("/" . $aliases[$item->id_sources][0] . "/", $aliases[$item->id_sources][1], $item->non_start_link);
            echo "<br>" . Html::a('start_link', $model->start_link, ['target' => '_blank']);
            echo "<br>" . Html::a('non_start_link', $model->non_start_link, ['target' => '_blank']);
            if (!$model->save()) my_var_dump($model->getErrors());
        }

        return $this->render('@app/views/console/processing');
    }

    /**
     * Updates an existing ParsingConfiguration model.
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
     * Deletes an existing ParsingConfiguration model.
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
     * Finds the ParsingConfiguration model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ParsingConfiguration the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ParsingConfiguration::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
