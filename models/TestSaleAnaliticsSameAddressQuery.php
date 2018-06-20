<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[SaleAnaliticsSameAddress]].
 *
 * @see SaleAnaliticsSameAddress
 */
class TestSaleAnaliticsSameAddressQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return SaleAnaliticsSameAddress[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return SaleAnaliticsSameAddress|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
