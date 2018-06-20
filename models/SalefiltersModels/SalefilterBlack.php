<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%Velikiy_Novgorod_salefilter_black}}".
 *
 * @property integer $id
 * @property integer $salefilter_id
 * @property integer $sale_id
 */
class SalefilterBlack extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%Velikiy_Novgorod_salefilter_black}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salefilter_id', 'sale_id'], 'integer'],
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
            'sale_id' => 'Sale ID',
        ];
    }
}
