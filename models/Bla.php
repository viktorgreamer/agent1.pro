<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "BlaBla".
 *
 * @property integer $id
 * @property string $text1
 * @property string $text2
 */
class Bla extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'BlaBla';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('cloud');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text1', 'text2'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text1' => 'Text1',
            'text2' => 'Text2',
        ];
    }
}
