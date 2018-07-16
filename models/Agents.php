<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%velikiy_novgorod_agents}}".
 *
 * @property integer $id
 * @property string $phone
 * @property string $date
 * @property integer $type
 * @property integer $count_ads
 * @property string $person
 * @property integer $avito_count
 * @property integer $irr_count
 * @property integer $yandex_count
 * @property integer $cian_count
 * @property integer $youla_count
 * @property integer $status
 * @property integer $person_type
 */
class Agents extends \yii\db\ActiveRecord
{

    const PERSON_TYPE_AGENT = 1;
    const PERSON_TYPE_HOUSEKEEPER = 2;
    const PERSON_TYPE_DEVELOPER = 3;

    public static function mapPersonType() {
        return [
            0=> 'Любой',
            2 => 'ХОЗЯИН',
            1 => 'АГЕНТ',
        ];
    }

    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_agents';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_agents";
        }
    }

    public static function setTablePrefix($prefix)
    {
        self::$tablePrefix = $prefix;
    }

    public function init()
    {
        $this->person_type = 0;
        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['type', 'count_ads', 'avito_count', 'irr_count', 'yandex_count', 'cian_count', 'youla_count', 'status', 'person_type'], 'integer'],
            [['person'], 'string'],
            [['phone'], 'string', 'max' => 100],
        ];
    }

    public function getVariants_of_person()
    {
        $body = '';
        $variants_of_name = ArrayHelper::getColumn(
            Sale::find()
                ->select('person')
                ->distinct()
                ->where(['phone1' => $this->phone])
                ->all(), 'person');
        foreach ($variants_of_name as $name) {
            if (trim($name)) $body .= " <br>" . Html::a(" SET " . $name, '#', [
                    'data' => ['id' => $this->id, 'name' => $name],
                    'class' => 'btn btn-primary btn-sm set-person-to-agent'
                ]);
        }
        return $body;
    }

    public function updateCounts()
    {

        $this->irr_count = Synchronization::find()->where(['id_sources' => 1])->andWhere(['phone1' => $this->phone])->count();
        $this->yandex_count = Synchronization::find()->where(['id_sources' => 2])->andWhere(['phone1' => $this->phone])->count();
        $this->avito_count = Synchronization::find()->where(['id_sources' => 3])->andWhere(['phone1' => $this->phone])->count();
        $this->youla_count = Synchronization::find()->where(['id_sources' => 4])->andWhere(['phone1' => $this->phone])->count();
        $this->cian_count = Synchronization::find()->where(['id_sources' => 5])->andWhere(['phone1' => $this->phone])->count();
        $this->count_ads = $this->irr_count + $this->yandex_count + $this->avito_count + $this->youla_count + $this->cian_count;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'date' => 'Date',
            'type' => 'Type',
            'count_ads' => 'Total',
            'person' => 'Person',
            'avito_count' => 'Avito Count',
            'irr_count' => 'Irr Count',
            'yandex_count' => 'Yandex Count',
            'cian_count' => 'Cian Count',
            'youla_count' => 'Youla Count',
            'status' => 'Status',
        ];
    }


    public function simple_test()
    {
        return 1;
    }

    public function set_agent_if_agent()
    {
        if (($this->irr_count > 1)
            or ($this->yandex_count > 1)
            or ($this->avito_count > 1)
            or ($this->youla_count > 1)
            or ($this->cian_count > 1))
            $this->person_type = 1;

    }


}
