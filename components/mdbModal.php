<?php
/**
 * Created by PhpStorm.
 * User: Екатерина
 * Date: 06.10.2017
 * Time: 11:08
 */

namespace app\components;


use yii\bootstrap\Widget;

class mdbModal extends Widget
{
    public $header = '';
    public $modalDialogClass = "";
    public $body = "";
    public $trigger = [];
    public $idModal = 'modalid';
    public $modalFooter = [];

    public function run()
    {
        return $this->render('mdb/mdb-modal',
            [
                'header' => $this->header,
                'modalDialogClass' => $this->modalDialogClass,
                'body' => $this->body,
                'trigger' => $this->trigger,
                'idModal' => $this->idModal,
                'modalFooter' => $this->modalFooter,
            ]);
    }

}