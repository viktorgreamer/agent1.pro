<?php

namespace app\components;

use yii;
use yii\base\Widget;
use app\models\User;



/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 27.09.2017
 * Time: 11:56
 */
class MDSideNav extends Widget
{
    public function run()
    {


        $session = Yii::$app->session;
        $id = $session->get('user_id');
        if ($id != 0) {

            $user = User::find()
                ->where(['user_id' => $id])
                ->one();

            return $this->render('mdb/side-nav-view',
                [
                    'user' => $user
                ]);
        }
    }

}