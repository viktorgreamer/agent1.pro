<?php

namespace app\modules\programmer\controllers;

use app\models\Addresses;
use app\models\Methods;
use app\models\Tags;

class TagsController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
public function actionActiveTag()
    {
        return $this->render('_active_tag');
    }

    public function actionAddTagToAddresses()
    {
        $id_tag = 29;
        Tags::setToMany($id_tag,'setToAllAddresses');

        return $this->render('index');
    }


}
