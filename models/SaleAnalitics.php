<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{sale_analitics}}".
 *
 * @property integer $id
 * @property integer $rooms_count
 * @property integer $grossarea
 * @property integer $average_price
 * @property integer $average_price_count
 * @property integer $house_type
 * @property integer $floorcount
 * @property integer $year
 * @property integer $type_of_plan
 * @property integer $years разброс в годах( например +-5)
 * @property integer $percent процент разброса площади при анализе вариантов
 * @property integer $period период анализа данных из истории например за 12 месяцев
 * @property integer $priceMAX максимальная цена учавствующая в статистике
 * @property integer $priceMIN минимальная цена учавствующая в статистике
 * @property integer $date время расчета аналитики
 */
class SaleAnalitics extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_analitics';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_analitics";
        }
    }

    public static function setTablePrefix($prefix)
    {
        self::$tablePrefix = $prefix;
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rooms_count', 'grossarea', 'average_price', 'average_price_count', 'floorcount', 'locality'], 'required'],
            [['rooms_count', 'grossarea', 'average_price', 'average_price_count', 'house_type', 'floorcount', 'year', 'type_of_plan'], 'integer'],
            [['locality'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'rooms_count' => 'rooms_count',
            'grossarea' => 'общая площадь',
            'average_price' => 'средняя цена в диапазоне',
            'average_price_count' => 'количество вариантов для подсчета средней цены',
            'house_type' => 'тип дома',
            'floorcount' => 'этажность дома',
            'year' => 'год постройки',
            'type_of_plan' => 'тип планировки',
        ];
    }


    public function IsExists()
    {
        // уникальность статистики расчитывается по следующим параметрам
        $isExists = Self::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['house_type' => $this->house_type])
            ->andwhere(['floorcount' => $this->floorcount])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['locality' => $this->locality])
            ->andwhere(['year' => $this->year])
            ->exists();
     //   if ($isExists) echo " <br>!!! данная статистика SaleAnalitics существует";
        return $isExists;


    }

    public function LoadToSale($sale)
    {
        $ExistedSaleAnalitics = SaleAnalitics::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['house_type' => $this->house_type])
            ->andwhere(['floorcount' => $this->floorcount])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['locality' => $this->locality])
            ->andwhere(['year' => $this->year])
            ->one();
      //  echo " <br> загружаем статистику SaleAnalitics";
        $sale->average_price = $ExistedSaleAnalitics->average_price;
        $sale->average_price_count = $ExistedSaleAnalitics->average_price_count;

    }

    // проставляем параметры в объект SaleAnalitics
    public function ExportParametersFromSale($sale)
    {
        // $sale - объект $sale, который пошел на анализ

        if ($this->percent == 0) $this->percent = 5;
        if ($this->period == 0) $this->period = 1;
        if ($this->years == 0) $this->years = 5;
        $this->rooms_count = $sale->rooms_count;
        $this->grossarea = round($sale->grossarea);
        $this->locality = $sale->locality;
        $this->house_type = $sale->house_type;
        $this->floorcount = $sale->floorcount;
        $this->year = $sale->year;
    }

    // главный метод которые рассчитывает статистику
    public function CalculateStatistic()
    {


       // $session = Yii::$app->session;

        // выбираем все sale_history по параметрам
        $all_sales_history = $this->getAllSaleHistoryObjects();

        // формируем простой массив с ценами для дальнейшей работы
        $prices = $this->getArrayOfPrices($all_sales_history);

        // формируем массив реальных цен (вычитаем откровенно бесполезные значения {заниженные и завышенные})
        $real_prices = $this->DeleteOverPrices($prices, 1, 0.8);

        $count_real_prices = count($real_prices);
        if ($count_real_prices != 0) {
            $this->average_price = round(array_sum($real_prices) / count($real_prices));

            $sales_history = $this->getAllSaleHistoryObjectsWithPriceLimits();

            //   $this->average_price = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
            $this->average_price_count = count($sales_history);
            $this->ids_sale_history = implode(",", $this->getIdsSaleHistory($all_sales_history));

        }


        // отладночные данные
      //  $session->setFlash('SaleAnalitics', $this);


        return $this;


    }

// группа вспомогательных методов
    public
    function DeleteOverPrices($prices, $RateUp, $RateDown)
    {

        $count = count($prices);
        $RealPrices = [];
        if ($count != 0) {
            // вычисляем среднюю цену
            $average = round(array_sum($prices) / $count);
            // пробегаемся по массиву и формируем массив новых цен входящих в диапазон $RateUp, $RateDown
            foreach ($prices as $price) {
                if (($price < $average * $RateUp) and ($price > $average * $RateDown)) {
                    array_push($RealPrices, (int)$price);
                }
            }
            $this->priceMIN = $RealPrices[0];
            $this->priceMAX = $RealPrices[count($RealPrices) - 1];

            // отладночные данные
         //   $session = Yii::$app->session;
          //  $session->setFlash('RealPrices', "<br>отсортированный массив RealPrices" . implode(",", $RealPrices));

            return $RealPrices;
        }


    }

    public
    function create_or_update_statistic_same_address_test_advansed($message = '', $sale, $persent = 5, $period = 12)
    {
        // $message  - сообщение для отладки и проверки работы
        // $sale - объект $sale, который пошел на анализ
        // $persent - процент разброса площади +-%
        // $period  - это период за который мы анализируем данный объект из истории

        $address = Addresses::findOne($sale->id_address);
        $updated_sale_analiticts_same_address = SaleAnaliticsSameAddress::find()
            ->where(['rooms_count' => $sale->rooms_count])
            ->andwhere(['id_address' => $sale->id_address])
            ->andwhere(['grossarea' => $sale->grossarea])
            ->exists();
        //  echo "<br>";
        //var_dump($updated_sale_analiticts_same_address);

        if (!$updated_sale_analiticts_same_address) {
            echo "<br>создаем статистику SaleAnaliticsSameAddress";
            $count_of_years = 5;

            // массив радиусов для для поиска похожих вариантов
            $radiuses = [300, 900, 1500, 2000, 3000, 5000];
            $this->rooms_count = $sale->rooms_count;
            $this->grossarea = $sale->grossarea;
            $this->id_address = $sale->id_address;

            foreach ($radiuses as $radius) {
                $this->radius = $radius;
                // ищем все похожие домиа в радиусах
                $id_addresses_in_radius = $address->getNearestSameIdAddresses($radius, $count_of_years);
                if (!empty($id_addresses_in_radius)) {
                    $this->average_price_count = Synchronization::find()
                        ->filterWhere(['rooms_count' => $sale->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($sale->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($sale->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_in_radius])
                        ->count();
                    // удаляем откровенные дубликаты которые портят статистику
                    $all_sales_history = Synchronization::find()
                        ->select(['id', 'phone1', 'id_address'])
                        ->asArray()
                        ->filterWhere(['rooms_count' => $sale->rooms_count])
                        ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                        ->andfilterwhere(['<', 'grossarea', ($sale->grossarea * (100 + $persent) / 100)])
                        ->andfilterwhere(['>', 'grossarea', ($sale->grossarea * (100 - $persent) / 100)])
                        ->andWhere(['in', 'id_address', $id_addresses_in_radius])
                        ->all();
                    // my_var_dump($all_sales_history);
                    $new_all_sales_history = $all_sales_history;
                    $i = 0;
                    // удаляем из массива дабуликаты по адресу и телефону
                    foreach ($all_sales_history as $sale_history) {
                        foreach ($new_all_sales_history as $new_sale_history) {
                            //если количество повторений равно более 1 то это явный дубликат
                            if (($sale_history['phone1'] == $new_sale_history['phone1']) and ($sale_history['id_address'] == $new_sale_history['id_address']) and ($sale_history['id'] != $new_sale_history['id'])) {
                                unset($all_sales_history[$i]);
                            }
                        }
                        $i++;

                    }
                    $unique_ids = [];
                    // формируем массив с ids
                    foreach ($all_sales_history as $item) {
                        array_push($unique_ids, $item['id']);
                    }

                    // выбираем цены из уникальных ids
                    $prices1 = Synchronization::find()
                        ->select('price')
                        ->Where(['in', 'id', $unique_ids])
                        ->asArray()
                        ->all();

                    $sales_history = Synchronization::find()
                        ->Where(['in', 'id', $unique_ids])
                        ->all();
                    $prices = [];

                    // формируем массив с ценами
                    foreach ($prices1 as $item) {
                        array_push($prices, $item['price']);
                    }


                    $new_prices = $prices;
                    if (count($new_prices) != 0) {
                        $average = round(array_sum($prices) / count($prices));
                        $simple_average_price = $average;


                        foreach ($prices as $price) {
                            if (($price > $average) or ($price * 1.8 < $average)) {
//                                echo " - " . $price;
                                unset($new_prices[array_search($price, $prices)]);
                                //   echo "-  удалили его";
                            }
                        }

                        asort($new_prices);
                        $count = count($new_prices);
//                        echo "<br>" . implode(",", $new_prices);
//                        echo "<br> кол-во элементов в массиве:".$count;
//                        echo "<br> брем только 30 % цен нижних цена в новом массиве".round(0.4*$count);
                        $new_prices_sliced = array_slice($new_prices, 0, round(0.4 * $count));
//                        echo "<br> новый массив после отрезки бесполезных вариантов : " . implode(",", $new_prices);
                        if (count($new_prices_sliced) != 0) $average = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
//                        echo "<br>новое cреднее число равно" . $average;
                        $sliced_average_price = $new_prices[round(0.4 * $count)];


                        if (count($new_prices_sliced) != 0) {
                            $sales_history = Synchronization::find()
                                ->filterWhere(['rooms_count' => $sale->rooms_count])
                                ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
                                ->andfilterwhere(['<', 'grossarea', ($sale->grossarea * (100 + $persent) / 100)])
                                ->andfilterwhere(['>', 'grossarea', ($sale->grossarea * (100 - $persent) / 100)])
                                ->andWhere(['in', 'id_address', $id_addresses_in_radius])
                                ->andWhere(['<', 'price', $average])
                                ->all();

                            $this->average_price = round(array_sum($new_prices_sliced) / count($new_prices_sliced));
                        }
                    }


                } else
                    break;
                // вычисляем количество вариантов из которых производилось вычисление средней цены


                $message .= "<br> количенство объектов" . $this->average_price_count . " при радиусе" . $radius;

                if ($this->average_price_count > 10) break;
            }
            $this->save();
        } else echo "<br>данная статистика уже существует SaleAnaliticsSameAddress";


        return [
            'message' => $message,
            'prices' => $prices,
            'new_prices' => $new_prices,
            'average' => $average,
            'simple_average_price' => $simple_average_price,
            'sliced_average_price' => $sliced_average_price,
            'sales_history' => $sales_history,
        ];


    }

    public
    function getCounts()
    {
        // подсчитываем все количество вариантов с учетом всех параметров
        return Synchronization::find()
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andwhere(['<>', 'geocodation', 9])
            ->andfilterwhere(['locality' => $this->locality])
            ->count();
    }

    public
    function getAllSaleHistoryObjects()
    {
        if ($this->house_type == 4) $this->house_type = 1;
        if ($this->house_type == 3) $this->house_type = 1;

        $all_sales_history = Synchronization::find()
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andFilterWhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andFilterWhere(['>=', 'year', ($this->year - $this->years)])
            ->andFilterWhere(['<=', 'year', ($this->year + $this->years)])
            ->andfilterwhere(['locality' => $this->locality])
            ->andwhere(['<=', 'floorcount', $this->floorcount + 1])
            ->andwhere(['>=', 'floorcount', $this->floorcount - 1])
            ->andwhere(['<>', 'geocodated', 9])
            ->andfilterwhere(['house_type' => $this->house_type])
            ->groupBy( 'id_address,floor')
            ->orderBy('price')
            ->all();
        if (!$all_sales_history) return false;
        return $all_sales_history;
    }


    public
    function getAllSaleHistoryObjectsWithPriceLimits()
    {
        if (($this->priceMAX) and ($this->priceMAX) and ($this->priceMAX > $this->priceMIN)) {
            $all_sales_history = Synchronization::find()
                ->Where(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
                ->andFilterWhere(['<=', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
                ->andFilterWhere(['>=', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
                ->andFilterWhere(['>=', 'year', ($this->year - $this->years)])
                ->andFilterWhere(['<=', 'year', ($this->year + $this->years)])
                ->andfilterwhere(['locality' => $this->locality])
                ->andwhere(['<=', 'floorcount', $this->floorcount + 1])
                ->andwhere(['>=', 'floorcount', $this->floorcount - 1])
                ->andfilterwhere(['house_type' => $this->house_type])
                ->andwhere(['>=', 'price', $this->priceMIN])
                ->andwhere(['<=', 'price', $this->priceMAX])
                ->orderBy('price')
                ->all();
        }

     //   $session = Yii::$app->session;

      //  $session->setFlash("CountAllSaleHistoryWithPriceLimits", count($all_sales_history));

        return $all_sales_history;
    }


// данный метод формируем из модели Salehistory массив цен ( например $prices = [12133,343434,4343435,6646];)
    public
    function getArrayOfPrices($all_sales_history)
    {

        $prices = [];

        // формируем массив с ценами
        if ($all_sales_history) {
            foreach ($all_sales_history as $item) {
                array_push($prices, (int)$item->price);
            }
        }


        // отладночные данные
      //  $session = Yii::$app->session;
      //  $session->setFlash('prices', "<br>отсортированный массив prices" . implode(",", $prices));


        return $prices;
    }

    public function getIdsSaleHistory($sales_history)
    {
        if (!$sales_history) return false;
        $unique_ids = [];
        foreach ($sales_history as $sale_history) {
            array_push($unique_ids, $sale_history['id']);
        }
        return $unique_ids;

    }


}
