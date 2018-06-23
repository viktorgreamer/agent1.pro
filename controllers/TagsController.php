<?php

namespace app\controllers;

use Yii;
use app\models\Tags;
use app\models\TagsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\components\TagsWidgets;
/**
 * TagsController implements the CRUD actions for Tags model.
 */
class TagsController extends Controller
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
                   // 'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function afterAction($action, $result)
    {
        $this->enableCsrfValidation = false;
        return parent::afterAction($action, $result); // TODO: Change the autogenerated stub
    }

    /**
     * Lists all Tags models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TagsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Tags model.
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
     * Creates a new Tags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Tags();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->updateAllCache() ;
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionRenderTags() {
        $session = Yii::$app->session;
        $plus_tags = $session->get('tags_id_to_search_sale');
        $minus_tags = $session->get('minus_tags_id_to_search_sale');
        $tags = '';
        if ($plus_tags) $tags .= "<a class='remove-plus-tags' href='#'> <i class='fa fa-plus  fa-fw text-primary'></i>".TagsWidgets::widget(['tags' => Tags::convertToArray($plus_tags),'br' => false])."</a><br>";
        if ($minus_tags) $tags .= "<a class='remove-minus-tags' href='#'> <i class='fa fa-minus  fa-fw text-danger'></i>".TagsWidgets::widget(['tags' => Tags::convertToArray($minus_tags),'br' => false])."</a><br>";

        return  $tags;

    }
    public function actionRemoveMinusTags() {

        $session = Yii::$app->session;

       $session->set('minus_tags_id_to_search_sale', '');
          return  '';

    }
    public function actionRemovePlusTags() {

        $session = Yii::$app->session;

       $session->set('tags_id_to_search_sale', '');
          return  '';

    }

    /**
     * Updates an existing Tags model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
          //  $model->updateAllCache();

            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Tags model.
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
     * Finds the Tags model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Tags::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    public function actionReset() {
       $TYPES = 'building,plan,object,locality,condition,deal';
      $TYPES_ARRAY = [
            '1' => 'Квартира',
            '2' => 'Планировка',
            '3' => 'Здание',
            '4' => 'Регион',
            '5' => 'Состояние',
            '6' => 'Сделка'
        ];
        $tags = Tags::find()->where(['type' => 'deal'])->all();
        echo count($tags);
        foreach ($tags as $tag) {
            $tag->type = '6';
           if (!$tag->save()) my_var_dump($tag->getErrors());
        }
    }

    public function actionTest() {
        return $this->render('test');
    }

}