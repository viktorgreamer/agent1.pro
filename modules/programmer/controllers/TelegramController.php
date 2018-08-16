<?php

namespace app\modules\programmer\controllers;

use Yii;


class TelegramController extends \yii\web\Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionBot()
    {


        Yii::$app->telegram->sendMessage([
            'chat_id' => 577542048,
            'text' => '/EXPENSIVE /NO_ADDRESS /NO_SHOW /STAR',
        ]);


        return $this->render('index');

    }

    public function actionInlineKeybords()
    {


        Yii::$app->telegram->sendMessage([

                'chat_id' => 577542048,
                'text' => 'JUST MWSSAGE ',

        ]);


        return $this->render('index');

    }

    public function actionGetChat()
    {

        echo "<br>";
        echo "<br>";
        echo "<br>";
        echo "<br>";
        if ($response = Yii::$app->telegram->getUpdates()) {
            foreach ($response->result as $item) {
                my_var_dump($item->message->text);

            }
        };


        return $this->render('index');

    }


}
