<?php

namespace app\modules\programmer\controllers;

use app\models\Renders;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

class UsersController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionCheckRole()
    {
        Renders::toAlert("jkj");

        $user = \Yii::$app->user->identity;
        my_var_dump($user->fullname);
        $auth = \Yii::$app->authManager;

        if (\Yii::$app->user->can('testUser')) {
            info("USER HAS TEST USER RULES", SUCCESS);
        }

        if (\Yii::$app->user->can('admin')) {
            info("USER HAS ADMIN USER RULES", SUCCESS);
        }







        return $this->render('index');
    }

    public function actionLogout()
    {
        Html::a("LOGOUT", Url::to(['user/logout']), ['target' => '_blank']);

        return $this->render('index');
    }

    public function actionDeleteTestUser()
    {
        $email = "abrakadabra@yandex.ru";

        if ($user = User::find()->where(['email' => $email])->one()) {
            $user->delete();

        }
        return $this->render('index');
    }

    public function actionRegistrationUser()
    {
        $email = "abrakadabra@yandex.ru";
        $last_name = "Пантелеев";
        $first_name = "Петр";
        $phone = "897792719274192847";

        if (!User::find()->where(['email' => $email])->one()) {
            $user = new User();
            $user->email = $email;
            $user->first_name = $first_name;
            $user->last_name = $last_name;
            $user->phone = $phone;

            if ($user->save()) {
                $user->setDefaultSettings();
                $user->save();
                $user->setDefaultRoles();
                echo Html::a("USER_VIEW", Url::to(['/user/view', 'id' => $user->id]), ['target' => '_blank']);
            }

        } else {
            info(" Пользователь с таким email существует", DANGER);
        }
        return $this->render('index');
    }

    public function actionSetUsersActive()
    {
        $users = User::find()->all();
        foreach ($users as $user) {
            $user->status = 1;
            $user->save();
        }
        return $this->render('index');
    }

}
