<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SmsApi;
use yii\data\Pagination;

/**
 * SmsApiSearch represents the model behind the search form about `app\models\SmsApi`.
 */
class SmsApiSearch extends SmsApi
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'price', 'user_id', 'status'], 'integer'],
            [['text_sms', 'person', 'address', 'name_list', 'phone', 'date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($id, $status)
    {
        $query = SmsApi::find();

        // add conditions that should always apply here

        // grid filtering conditions
        $query->andFilterWhere(['id_list' => $id]);
        $query->andFilterWhere(['status' => $status]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;

    }

    public function search_for_edit($id, $status)
    {
        $query = SmsApi::find();

        // add conditions that should always apply here



        // grid filtering conditions
        $query->andFilterWhere(['id_list' => $id]);
        $query->andFilterWhere(['status' => $status]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $rows = $query->offset($pages->offset)
            ->limit(1000)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;

    }

    public function search_without_status($id)
    {
        $query = SmsApi::find();

        // add conditions that should always apply here



        // grid filtering conditions
        $query->andFilterWhere(['id_list' => $id]);


        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;
    }

    public function search_delayed($id)
    {
        $query = SmsApi::find();

        // add conditions that should always apply here



        // grid filtering conditions
        $query->andFilterWhere(['id_list' => $id]);
        $query->andFilterWhere(['status' => 2]);

        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        $rows = $query->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        $data = [
            'data' => $rows,
            'pages' => $pages
        ];

        return $data;
    }
}
