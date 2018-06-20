<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%sms_api}}".
 *
 * @property integer $id
 * @property string $text_sms
 * @property string $person
 * @property string $address
 * @property integer $price
 * @property integer $user_id
 * @property string $name_list
 * @property string $phone
 * @property string $date
 * @property integer $status
 */
class SmsApi extends \yii\db\ActiveRecord
{
    const STATUS_ARRAY = [
        '0' => "Нередактированные",
        '1' => "Сохраненные",
        '2' => "Отложенные",
        '3' => "Посланные"];
    /**
     * @inheritdoc
     */
    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sms_api';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sms_api";
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
            [['text_sms'], 'string'],
            [['id_list', 'status'], 'safe'],
            [['price', 'user_id', 'status', 'id_sale', 'rooms_count'], 'integer'],
            [['address'], 'string', 'max' => 40],
            [['phone'], 'string', 'max' => 11],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text_sms' => 'Text Sms',
            'person' => 'Person',
            'address' => 'Address',
            'price' => 'Price',
            'user_id' => 'User ID',
            'name' => 'Name',
            'phone' => 'Phone',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    public function formName()
    {
        return '';
    }

    public static function getMyLists()
    {
        return ArrayHelper::map(\app\models\SmsApi::find()->all(), 'id_list', 'name');
    }

}
