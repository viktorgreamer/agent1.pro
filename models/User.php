<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "user".
 *
 * @property integer $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property string $phone
 * @property string $password
 * @property string $auth_date
 * @property integer $test_date
 * @property integer $exp_date
 * @property string $city
 * @property string $type
 * @property string $city_rus
 * @property string $semysms_token
 * @property string $vk_token
 * @property string $list_or_vk_groups
 * @property integer $money
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public $profile;


    public static function getAvailableUsersAsArray()
    {
        $session = Yii::$app->session;
        $current_user_id = $session->get('user_id');
        $current_user = User::findOne($current_user_id);

        $users = User::find()->where(['city_modules' => $session->get('city_module')])->andWhere(['<>', 'user_id', $current_user_id])->all();
        $array = [];
        $array[$current_user_id] = $current_user->fullname;
        foreach ($users as $user) {
            $array[$user->user_id] = $user->fullname;
        }
        return $array;

    }

    public function getFullname()
    {

        return $this->first_name . " " . $this->last_name;

    }


    public function to_session()
    {
        $session = Yii::$app->session;
        $session->set('last_name', $this->last_name);
        $session->set('first_name', $this->first_name);
        $session->set('city', $this->city);
        $session->set('phone', $this->phone);
        $session->set('user_id', $this->user_id);
        $session->set('city_module', $this->city_modules);
        $session->set('irr_id_partners', $this->irr_id_partners);
        // закидываем данные модуля в сессию чтобы брать их где угодно в программе
        $module = Control::find()->where(['region' => $this->city_modules])->one();
        // if ($module) echo " загрузили модуль ". $module->region_rus;
        $session->set('module', $module);

    }

    public function is_unique()
    {
        if (User::find()->where(['identity' => $this->identity])->count() == 0) {
            return true;
        } else return false;

    }


    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['user_id', 'test_date', 'exp_date', 'money'], 'integer'],
            [['rent', 'sale', 'extra'], 'boolean'],
            [['phone', 'list_or_vk_groups', 'identity', 'network'], 'string'],
            [['auth_date'], 'safe'],
            [['first_name', 'last_name', 'email', 'password', 'semysms_token', 'vk_token'], 'string', 'max' => 255],
            [['city', 'city_modules'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'Агент',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'phone' => 'Phone',
            'password' => 'Password',
            'auth_date' => 'Auth Date',
            'test_date' => 'Test Date',
            'exp_date' => 'Exp Date',
            'city' => 'City',
            'type' => 'Type',
            'city_rus' => 'City Rus',
            'semysms_token' => 'Semysms Token',
            'vk_token' => 'Vk Token',
            'list_or_vk_groups' => 'List Or Vk Groups',
            'money' => 'Money',
        ];
    }

    public function login($ulogin)
    {


        if ($user = User::find()
            ->where(['user_id' => $ulogin->user_id])
            ->limit(1)
        ) {
            $current_password = md5($ulogin->ident . $ulogin->seed);
            //print_r($db_user);
            if ($user->password === $current_password) {
                $_SESSION['user_id'] = $user['user_id'];
            } else {
                unset($_SESSION['user_id']);
            }
        }


    }
}
