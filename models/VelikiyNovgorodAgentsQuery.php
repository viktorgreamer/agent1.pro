<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[Agents]].
 *
 * @see Agents
 */
class VelikiyNovgorodAgentsQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Agents[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Agents|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
