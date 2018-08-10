<?php

namespace app\models;

use Yii;
use yii\base\NotSupportedException;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;


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
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const ROLE_TESTUSER = 'testUser';
    const ROLE_ADMIN = 'admin';
    const ROLE_PLAN1 = 'plan1';
    const ROLE_PLAN2 = 'plan2';
    const ROLE_PLAN3 = 'plan3';

    const TEST_PERIOD = 2; // DAys


    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }


    public static function findIdentity($id)
    { $user = static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
      if ($user) $user->to_session();
        return $user;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    public static function findByPasswordResetToken($token)
    {

        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    public static function isPasswordResetTokenValid($token)
    {

        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /**
     * @inheritdoc
     */
    public $profile;


    public static function getAvailableUsersAsArray()
    {
        $session = Yii::$app->session;
        $user_id = Yii::$app->user->id;
        $user = Yii::$app->user->identity;

        $users = User::find()->where(['city_modules' => $session->get('city_module')])->andWhere(['<>', 'id', $user_id])->all();
        $array = [];
        $array[$user_id] = $user->fullname;
        foreach ($users as $user) {
            $array[$user->id] = $user->fullname;
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
        $session->set('user_id', $this->id);
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

    public function formName()
    {
        return '';
    }


    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'required'],
            [['id', 'test_date', 'exp_date', 'money'], 'integer'],
            [['email'], 'email'],
            //  [['email'], 'unique'],
            [['rent', 'sale', 'extra'], 'boolean'],
            [['phone', 'list_or_vk_groups', 'identity', 'network'], 'string'],
            [['auth_date', 'phone', 'last_name', 'first_name'], 'safe'],
            [['first_name', 'last_name', 'email', 'password', 'semysms_token', 'vk_token'], 'string', 'max' => 255],
            [['city', 'city_modules'], 'string', 'max' => 100],
         //   [['reCaptcha'], \himiklab\yii2\recaptcha\ReCaptchaValidator::className(), 'secret' => 'your secret key', 'uncheckedMessage' => 'Please confirm that you are not a bot.']

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Id',
            'first_name' => 'Имя',
            'last_name' => 'Фамилия',
            'email' => 'Email',
            'phone' => 'Телефоны',
            'password' => 'Пароль',
            'auth_date' => 'Дата регистрации',
            'test_date' => 'Тестовый период',
            'exp_date' => 'Дата окончания',
            'city' => 'Город',
            'type' => 'Type',
            'city_rus' => '',
            'semysms_token' => 'Semysms Token',
            'vk_token' => 'Vk Token',
            'list_or_vk_groups' => 'Список групп в контакте',
            'money' => 'Баланс',
        ];
    }

    public function setDefaultSettings()
    {
        $this->auth_date = time();
        $this->test_date = time() + self::TEST_PERIOD * 72 * 60 * 60;
        $this->status = self::STATUS_ACTIVE;
        $this->city_modules = 'Velikiy_Novgorod';
        $this->city= "Великий Новгород";
        Notifications::VKMessage(" ЗАРЕГИСТРИРОВАЛСЯ USER ".$this->email);
    }

    public function setDefaultRoles()
    {
        $userRole = Yii::$app->authManager->getRole('testUser');
        Yii::$app->authManager->assign($userRole, $this->id);

    }

    public function afterDelete()
    {
      //  $th
        parent::afterDelete(); // TODO: Change the autogenerated stub
    }

    public function login($ulogin)
    {


        if ($user = User::find()
            ->where(['id' => $ulogin->user_id])
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
