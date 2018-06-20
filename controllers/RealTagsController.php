<?php

namespace app\controllers;

use app\models\Addresses;
use Yii;
use app\models\RealTags;
use app\models\RealTagsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RealTagsController implements the CRUD actions for RealTags model.
 */
class RealTagsController extends Controller
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
     * Lists all RealTags models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RealTagsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single RealTags model.
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
     * Creates a new RealTags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RealTags();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Creates a new RealTags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionTag($tag_id, $parent_id, $type = 'sale')
    {
        $session = Yii::$app->session;
        $user_id = $session->get('user_id');
        $return = RealTags::setRealTags($parent_id, $tag_id, $type, $user_id);
        if ($type != 'address') return $return;
        // return $this->render('test');
    }

    /**
     * Creates a new RealTags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */

    public function actionTagAddress($tag_id, $address_id)
    {
        $tag = new RealTags();
        $message = $tag->setToAddress($address_id, $tag_id);
        return $message;
    }

    public function actionReorderTagsAddress()
    {
        $ids_addresses = [3501, 8000];
        $addresses = Addresses::find()->where(['>', 'id', $ids_addresses[0]])->andWhere(['<', 'id', $ids_addresses[1]])
            ->all();
        foreach ($addresses as $address) {
            if ($address->tags_id) {
                echo "<hr>" . $address->tags_id;
                $tags = explode(',', $address->tags_id);
                foreach ($tags as $tag) {
                    if ($tag != '') {
                        $existed_tag = RealTags::find()->where(['id_address_tag' => $address->id])->andWhere(['tag_id' => $tag])->one();
                        if ($existed_tag) {
                            //   $existed_tag->delete();
                            echo "<br> удалили tag=" . $tag;
                        } else {

                            $realtag = new RealTags();
                            $realtag->tag_id = $tag;
                            $realtag->id_address_tag = $address->id;
                            if (!$realtag->save()) my_var_dump($realtag->getErrors());
                            echo "<br> set tag=" . $tag;
                        }
                    }


                }
            }
        }
        return $this->render('test');
    }


    public function actionTagsAddresses()
    {
        $session = Yii::$app->session;

        $id_addresses = $session->get('addresses');
        $tags_id = explode(",", $session->get('tags_id'));
        $session->remove('info');
        $tag = new RealTags();

        if (($id_addresses) and ($tags_id)) {
            $session->setFlash('info', 'SUCCESS');
            foreach ($id_addresses as $id_address) {
                foreach ($tags_id as $tag_id) {
                    $tag->setToAddress($id_address, $tag_id);
                }
            }
            $session->remove('id_addresses');
            $session->remove('tags_id');
            return $this->renderPartial('info', ['message' => " Успешно применили" . count($tags_id) . " tags"]);
        } else return $this->renderPartial('info', ['message' => "nothing to add"]);


    }

    public function actionTagToSearch($tag_id)
    {
        $session = Yii::$app->session;

        $current_tags = $session->get('tags_id_to_search_sale');

        if ($current_tags != '') {
            //  echo " если в списке что-то есть";
            // получаем список уже имеющихся tags
            $exist_tags = explode(',', $current_tags);
            // если там данный tag есть то удаляем его если нет то добавляем
            if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
            else  array_push($exist_tags, $tag_id);
            // переводим массив в список
            if (count($exist_tags) == 0) $current_tags = ''; else $current_tags = implode(",", $exist_tags);

        } else {
            // echo " список был пустой";
            $current_tags = $tag_id;
        } // если ничего не было, то сразу добавляем
        $session->set('tags_id_to_search_sale', $current_tags);
        return $current_tags;
    }


    /**
     * Creates a new RealTags model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionTagSaleFilter($tag_id, $salefilter_id)
    {
        $tag = new RealTags();
        $tag->setToSaleFilter($salefilter_id, $tag_id);

    }

    public function actionTagSaleList($tag_id, $salelist_id)
    {
        $tag = new RealTags();
        $tag->setToSaleList($salelist_id, $tag_id);

    }

    /**
     * Updates an existing RealTags model.
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
     * Deletes an existing RealTags model.
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
     * Finds the RealTags model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RealTags the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RealTags::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
