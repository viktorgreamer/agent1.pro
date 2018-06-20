<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property integer $id
 * @property string $phone
 * @property string $name
 * @property string $description
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['phone'], 'string', 'max' => 256],
            [['name'], 'string', 'max' => 256],
        ];
    }
    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'phone' => 'Phone',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }
    public function send() {
        Yii::$app->mailer->compose()
            ->setTo('an.viktory@gmail.com')
            ->setFrom(['viktorgreamer1@yandex.ru' => 'agent1.pro'])
            ->setSubject('новая заявка')
            ->setTextBody(" ".$this->name ."<br> ".$this->description."<br> ".$this->phone)
            ->send();

    }
}
