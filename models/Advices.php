<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "advices".
 *
 * @property integer $id
 * @property string $title
 * @property string $q
 * @property integer $type
 */
class Advices extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'advices';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type'], 'integer'],
            [['title', 'q'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'q' => 'Q',
            'type' => 'Type',
        ];
    }
}
