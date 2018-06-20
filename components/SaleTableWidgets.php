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


class SaleTableWidgets extends Widget
{
    public $sales;
    public $salefilter;
    public $salelist;
    public $options;
    public $controls;
    public $totalcount;
    public $type;

    public function run()
    {

        if ($this->type == 'web')  return $this->render('sale-table-web',
            [
                'sales' => $this->sales,
                'salelist' => $this->salelist,
                'options' => $this->options,

            ]);
         else return $this->render('sale-table',
            [
                'sales' => $this->sales,
                'totalcount' => $this->totalcount,
                'salefilter' => $this->salefilter,
                'salelist' => $this->salelist,
                'controls' => $this->controls,
                'options' => $this->options,

            ]);

    }
}


