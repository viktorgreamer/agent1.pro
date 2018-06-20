<?php

namespace app\models;

/**
 * This is the ActiveQuery class for [[ControlParsing]].
 *
 * @see ControlParsing
 */
class ParsingcontrolQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return ControlParsing[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return ControlParsing|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
