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


class SaleAnaliticsTableWidgets extends Widget
{
    public $sales;
    public $salefilter;

    public function run()
    {

        return $this->render('sale-analitics-table',
            [
                'sales' => $this->sales,
                'salefilter' => $this->salefilter ]);
    }
}

