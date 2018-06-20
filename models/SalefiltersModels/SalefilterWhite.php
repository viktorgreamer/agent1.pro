<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%Velikiy_Novgorod_salefilter_white}}".
 *
 * @property integer $id
 * @property integer $salefilter_id
 * @property integer $sale_id
 */
class SalefilterWhite extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%Velikiy_Novgorod_salefilter_white}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['salefilter_id', 'sale_id'], 'required'],
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
