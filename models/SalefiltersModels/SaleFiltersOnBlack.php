<?php

namespace app\models\SalefiltersModels;
use app\models\SaleFilters;

use Yii;

/**
 * This is the model class for table "Velikiy_Novgorod_sale_filters_on_black".
 *
 * @property integer $id
 * @property integer $salefilter_id
 * @property integer $rooms_count
 * @property integer $date
 * @property integer $id_address
 * @property integer $floor
 * @property integer $grossarea

 */
class SaleFiltersOnBlack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_filters_on_black';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_filters_on_black";
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
            [['salefilter_id', 'rooms_count', 'date', 'id_address', 'floor', 'grossarea'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'salefilter_id' => 'Salefilter ID',
            'rooms_count' => 'Rooms Count',
            'date' => 'Date',
            'id_address' => 'Id Address',
            'floor' => 'Floor',
            'grossarea' => 'Grossarea',
            ];
    }

    public function IsInTemplate($sale) {
        if (($this->id_address == $sale->id_address)
            and ($this->rooms_count == $sale->rooms_count)
            and ($this->floor == $sale->floor)
            and ($this->grossarea < $sale->grossarea*(100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE)/100)
            and ($this->grossarea > $sale->grossarea*(100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE)/100)
           )
            return true;
    }
}
