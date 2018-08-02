<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Synchronization;

/**
 * SynchronizationSearch represents the model behind the search form about `app\models\Synchronization`.
 */
class SynchronizationSearch extends Synchronization
{

    public $date_of_check_up;
    public $date_of_check_down;
    public $date_of_die_up;
    public $date_of_die_down;
    public $date_of_start_up;
    public $date_of_start_down;
    public $sort_by;
    public $log;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_sources', 'disactive', 'price', 'rooms_count', 'sort_by', 'log'], 'integer'],
            [['id_in_source'], 'string'],
            [['url', 'title', 'address', 'status', 'sync', 'geocodated', 'processed', 'load_analized', 'tags_autoload', 'parsed', 'moderated', 'id_in_source', 'disactive',
                'date_of_check_up', 'date_of_check_down', 'date_of_die_up', 'date_of_die_down', 'date_of_start_up', 'date_of_start_down'], 'safe'],
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

    public function init()
    {
        parent::init();
        $this->sync = 0;
        $this->geocodated = 0;
        $this->processed = 0;
        $this->load_analized = 0;
        $this->parsed = 0;
        $this->moderated = 0;
        $this->disactive = 10;
        $this->status = 0;
//        $this->date_of_check_up = time();
//        $this->date_of_check_down = time() - 10 * 24 * 60 * 60;
//        $this->date_of_die_up = time();
//        $this->date_of_die_down = time() - 10 * 24 * 60 * 60;
//        $this->date_of_start_up = time();
//        $this->date_of_start_down = time() - 10 * 24 * 60 * 60;

    }

    public function beforeValidate()
    {
        if ($this->date_of_check_up) $this->date_of_check_up = strtotime($this->date_of_check_up);
        if ($this->date_of_check_down) $this->date_of_check_down = strtotime($this->date_of_check_down);

        if ($this->date_of_die_up) $this->date_of_die_up = strtotime($this->date_of_die_up);
        if ($this->date_of_die_down) $this->date_of_die_down = strtotime($this->date_of_die_down);

        if ($this->date_of_start_up) $this->date_of_start_up = strtotime($this->date_of_start_up);
        if ($this->date_of_start_down) $this->date_of_start_down = strtotime($this->date_of_start_down);

        return parent::beforeValidate(); // TODO: Change the autogenerated stub
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
        $query = Synchronization::find();
        $query->from(['sync' => Synchronization::tableName()]);
        // присоединяем связи
         $query->joinWith(['agent AS agent']);
         $query->joinWith(['addresses AS address']);
        // $query->joinWith(['similar AS sim']);
        //  $query->joinWith(['logs AS logs']);
       //   $query->joinWith(['plogs AS plogs']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            echo " <h5>error validation</h5>";
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if ($this->id) $query->andFilterWhere(['sync.id' => $this->id]);
        if ($this->log) $query->andFilterWhere(['logs.type' => $this->log]);

        if ($this->id_sources) $query->andFilterWhere(['sync.id_sources' => $this->id_sources]);
        //
        if ($this->sync) $query->andFilterWhere(['sync.sync' => $this->sync]);

        // если надо вывести опреденного статуса
        if (($this->disactive != 10) and ($this->disactive != 6)) $query->andFilterWhere(['sync.disactive' => $this->disactive]);
        if ($this->disactive == 6) {

            $query->andWhere(['IS', 'sync.disactive',null]);
        }
        // если надо вывести не удаленные
        if ($this->status) $query->andWhere(['sync.status' => $this->status]);
        // если надо определенный стасус геокодирования
        if ($this->geocodated) $query->andFilterWhere(['sync.geocodated' => $this->geocodated]);

        if ($this->moderated) $query->andFilterWhere(['sync.moderated' => $this->moderated]);
        // если нужно опеределенный статус парсинга
        if ($this->parsed) $query->andFilterWhere(['sync.parsed' => $this->parsed]);
        // если нужно опеределенный статус обработки
        if ($this->processed) $query->andFilterWhere(['sync.processed' => $this->processed]);
        // если нужно опеределенный статус load_analized
        if ($this->load_analized) $query->andFilterWhere(['sync.load_analized' => $this->load_analized]);

        // если нужно определенное количество комнат
        if ($this->rooms_count) $query->andFilterWhere(['sync.rooms_count' => $this->rooms_count]);
        if ($this->id_in_source) $query->andFilterWhere(['sync.id_in_source' => $this->id_in_source]);

        // временные фильтры
        if ($this->date_of_check_up) $query->andFilterWhere(['<', 'sync.date_of_check', $this->date_of_check_up + 86400]);
        if ($this->date_of_check_down) $query->andFilterWhere(['>', 'sync.date_of_check', $this->date_of_check_down]);

        if ($this->date_of_start_up) $query->andFilterWhere(['<', 'sync.date_start', $this->date_of_start_up + 86400]);
        if ($this->date_of_start_down) $query->andFilterWhere(['>', 'sync.date_start', $this->date_of_start_down]);

        if ($this->date_of_die_up) $query->andFilterWhere(['<', 'sync.date_of_die', $this->date_of_die_up + 86400]);
        if ($this->date_of_die_down) $query->andFilterWhere(['>', 'sync.date_of_die', $this->date_of_die_down]);

        //  $query->orderBy(['sync.date_of_check' => SORT_DESC]);

        switch ($this->sort_by) {
            case SaleFilters::SORTING_ID:
                $query->orderBy(['sync.id' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_PRICE_ASC:
                $query->orderBy(['sync.price' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_PRICE_DESC:
                $query->orderBy(['sync.price' => SORT_DESC]);
                break;
            case SaleFilters::SORTING_DATE_START_ASC:
                $query->orderBy(['sync.date_start' => SORT_DESC]);
                break;

            case SaleFilters::SORTING_DATE_START_DESC:
                $query->orderBy(['sync.date_start' => SORT_ASC]);
                break;
            case SaleFilters::SORTYNG_DATE_OF_CHECK_ASC:
                $query->orderBy(['sync.date_of_check' => SORT_ASC]);
                break;

            case SaleFilters::SORTYNG_DATE_OF_CHECK_DESC:
                $query->orderBy(['sync.date_of_check' => SORT_DESC]);
                break;

            case SaleFilters::SORTING_ID_ADDRESS_ASC:
                $query->orderBy(['sync.id_address' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_ID_ADDRESS_DESC:
                $query->orderBy(['sync.id_address' => SORT_DESC]);
                break;


        }
        $query->groupBy('sync.id');

       $session = Yii::$app->session;
        $dataProviderQuery = clone $dataProvider->query;
    if ($synchronisations = $dataProviderQuery->select('sync.id')->column()) {

        $session->set('synchronisations', $synchronisations);
    }
        return $dataProvider;
    }
}
