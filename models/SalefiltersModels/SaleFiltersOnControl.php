<?php

namespace app\models\SalefiltersModels;
use app\models\SaleFilters;

use Yii;

/**
 * This is the model class for table "Velikiy_Novgorod_sale_filters_on_control".
 *
 * @property integer $id
 * @property integer $id_salefilter
 * @property integer $date
 * @property integer $id_similar
 * @property integer $price
 */
class SaleFiltersOnControl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_filters_on_control';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_filters_on_control";
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
            [['id_salefilter', 'date','price','id_similar'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_salefilter' => 'Salefilter ID',
            'date' => 'Date',
            'id_similar' => 'Id similar',
            'price' => 'Price',
        ];
    }

    public function IsInTemplate($sale) {
        if (($this->id_similar == $sale->id_similar)
            and ($this->price < $sale->price))
            return true;
    }
}
