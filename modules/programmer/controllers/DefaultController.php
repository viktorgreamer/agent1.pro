<?php

namespace app\modules\programmer\controllers;

use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    /**
     * Renders the index view for the module
     * @return string
     */

    public function init()
    {

        $this->layout = "@app/modules/programmer/views/layouts/main.php";
        parent::init();

        // custom initialization code goes here
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
