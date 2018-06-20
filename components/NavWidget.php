<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 26.01.2017
 * Time: 11:42
 */

namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;



class NavWidget extends Widget

{
    public function run()
    {


        $session = Yii::$app->session;
        $id = $session->get('user_id');
        if ($id != 0) {

            $user = User::find()
                ->where(['user_id' => $id])
                ->one();

            return $this->render('nav-view',
                [
                    'user' => $user
                ]);
        }
    }
}