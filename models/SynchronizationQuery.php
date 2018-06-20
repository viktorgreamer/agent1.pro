<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Synchronization]].
 *
 * @see Synchronization
 */
class SynchronizationQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Synchronization[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Synchronization|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public static function find($type)
    {

        switch ($type) {
            case READY_FOR_SALESIMILAR_CHECK:
                return Synchronization::find()->where(['id_similar' => 0])
                    ->andwhere(['IS NOT', 'id_address', NULL])
                    ->andwhere(['>', 'price', 0])
                    ->andwhere(['IS NOT', 'grossarea', NULL])
                    ->andwhere(['IS NOT', 'floor', NULL])
                    ->andwhere(['IS NOT', 'rooms_count', NULL]);
        }


    }
}
