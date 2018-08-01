<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 25.07.2018
 * Time: 8:51
 */
namespace app\models\UserModels;

use app\models\User;

class SignInForm extends \yii\base\Model
{
    public $email;
    public $password;
    public $remember_me;

    public function formName()
    {
        return '';
    }

    public function rules()
    {
        return [
            [['email'], 'email'],
            [['password'], 'string'],

        ];
    }



    public function signup() {
        return User::find()->where(['email' => $this->email])->andWhere(['password' => $this->password])->one();

    }



}