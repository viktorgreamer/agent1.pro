<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 12.03.2018
 * Time: 10:34
 */

namespace app\models;
use app\models\Sale;
use yii\db\Query;


/* @var $salefilter SaleFilters */
/* @var $query Query */
class SaleFilterSearchBy extends SaleFilters
{
    
    public $query;
    
    public function searchBy($salelfilter) {
        
        $this->begin();
        
        
        
    }
    protected function begin(){
        $this->query = Sale::find();
    }


   
// уникализация
    protected function uniquing()
    {
        $query = $this->query;
        switch ($_GET['unique']) {
            case SaleFilters::UNIQUE_MAIN :
                break;

            case SaleFilters::UNIQUE_ROW:
                $query->groupBy('s.id_similar,s.phone1');
                break;

            case SaleFilters::UNIQUE_OBJECT:
                $query->groupBy('s.id_similar');
                break;

        }
        $this->query = $query;
        
    }

    protected function sorting()
    {
        $query = $this->query;
        switch ($_GET['sort_by']) {
            case SaleFilters::SORTING_ID:
                $query->orderBy(['s.id' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_PRICE_ASC:
                $query->orderBy(['s.price' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_PRICE_DESC:
                $query->orderBy(['s.price' => SORT_DESC]);
                break;
            case SaleFilters::SORTING_DATE_START_ASC:
                $query->orderBy(['s.date_start' => SORT_DESC]);
                break;

            case SaleFilters::SORTING_DATE_START_DESC:
                $query->orderBy(['s.date_start' => SORT_ASC]);
                break;

            case SaleFilters::SORTING_ID_ADDRESS_ASC:
                $query->orderBy(['s.id_address' => SORT_ASC]);
                break;
            case SaleFilters::SORTING_ID_ADDRESS_DESC:
                $query->orderBy(['s.id_address' => SORT_DESC]);
                break;


        }
        $this->query = $query;

    }
    // подключение связей
    protected function relations()
    {
            $query = $this->query;
            $query->from(['s' => Sale::tableName()]);
            // присоединяем связи
            $query->joinWith(['agent AS agent']);
            $query->joinWith(['addresses AS address']);
            $query->joinWith(['similarNew AS sim']);
        }




}