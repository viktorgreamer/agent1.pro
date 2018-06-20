<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "Velikiy_Novgorod_agents".
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
 */


class Agents extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['date'], 'safe'],
            [['type', 'count_ads'], 'integer'],
            [['phone'], 'string', 'max' => 100],
            [['person'], 'string', 'max' => 255],
            [['phone'], 'unique'],
        ];
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
            'count_ads' => 'Count Ads',
            'person' => 'Person',
            'avito_count' => 'Avito Count',
            'irr_count' => 'Irr Count',
            'yandex_count' => 'Yandex Count',
            'cian_count' => 'Cian Count',
            'youla_count' => 'Youla Count',
            'status' => 'Status',
        ];
    }

    public static function find()
    {
        return new AgentsQuery(get_called_class());
    }

}
