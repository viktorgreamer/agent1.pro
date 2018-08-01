<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 16.04.2017
 * Time: 22:17
 */

namespace app\components;

use yii\base\Widget;
use yii\helpers\Html;
use yii\base\Widgets;
use yii\widgets\ActiveForm;
use yii\widgets\LinkPager;
use yii\widgets\ActiveField;
use yii\widgets\Pjax;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;


class SaleFilterWidgets extends Widget
{
    public $salefilter;
    public $type;

    public $options;


    public function run()
    {
        if ($this->type == 'client') {
            return $this->render('client-view',
                [

                    'salefilter' => $this->salefilter,

                    'options' => $this->options,

                ]);
        } elseif ($this->type == 'web') {
            return $this->render('_salefilters_view_promo',
                [

                    'salefilter' => $this->salefilter,

                    'options' => $this->options,

                ]);
        } else {
            return $this->render('salefilter-view',
                [

                    'salefilter' => $this->salefilter,

                    'options' => $this->options,

                ]);
        }


    }
}


