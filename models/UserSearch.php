<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\User;

/**
 * UserSearch represents the model behind the search form about `app\models\User`.
 */
class UserSearch extends User
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'test_date', 'exp_date', 'extra', 'rent', 'sale', 'money'], 'integer'],
            [['network', 'identity', 'first_name', 'last_name', 'email', 'phone', 'password', 'auth_date', 'city', 'city_modules', 'semysms_token', 'vk_token', 'list_or_vk_groups', 'irr_id_partners'], 'safe'],
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
    public function search($params)
    {
        $query = User::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'auth_date' => $this->auth_date,
            'test_date' => $this->test_date,
            'exp_date' => $this->exp_date,
            'extra' => $this->extra,
            'rent' => $this->rent,
            'sale' => $this->sale,
            'money' => $this->money,
        ]);

        $query->andFilterWhere(['like', 'network', $this->network])
            ->andFilterWhere(['like', 'identity', $this->identity])
            ->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'password', $this->password])
            ->andFilterWhere(['like', 'city', $this->city])
            ->andFilterWhere(['like', 'city_modules', $this->city_modules])
            ->andFilterWhere(['like', 'semysms_token', $this->semysms_token])
            ->andFilterWhere(['like', 'vk_token', $this->vk_token])
            ->andFilterWhere(['like', 'list_or_vk_groups', $this->list_or_vk_groups])
            ->andFilterWhere(['like', 'irr_id_partners', $this->irr_id_partners]);

        return $dataProvider;
    }
}
