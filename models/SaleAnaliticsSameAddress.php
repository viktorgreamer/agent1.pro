<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%test_sale_analitics_same_address}}".
 *
 * @property integer $id
 * @property integer $id_address
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
 *  @property integer $date время расчета аналитики
 */
class SaleAnaliticsSameAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_analitics_same_address';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_analitics_same_address";
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
            [['id_address', 'rooms_count', 'grossarea', 'house_type', 'floorcount', 'average_price', 'average_price_count'], 'integer'],
            [['rooms_count', 'grossarea'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_address' => 'Id Address',
            'rooms_count' => 'Rooms Count',
            'grossarea' => 'Grossarea',
            'house_type' => 'House Type',
            'floorcount' => 'Floorcount',

            'average_price' => 'Average Price',

            'average_price_count' => 'Average Price 400',
        ];
    }

    /**
     * @inheritdoc
     * @return TestSaleAnaliticsSameAddressQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new TestSaleAnaliticsSameAddressQuery(get_called_class());
    }

    public function IsExists()
    {
        // уникальность статистики расчитывается по следующим параметрам
         $isExists = Self::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
      //  if ($isExists) echo " <br>!!! данная статистика SaleAnaliticsSameAddress существует";


        return $isExists;


    }

    // проставляем параметры в объект SaleAnaliticsSameAddress
    public function ExportParametersFromSale($sale)
    {
        // $sale - объект $sale, который пошел на анализ

        if ($this->percent == 0) $this->percent = 5;
        if ($this->period == 0) $this->period = 6;
        if ($this->years == 0) $this->years = 5;
        $this->rooms_count = $sale->rooms_count;
        $this->grossarea = round($sale->grossarea);
        $this->id_address = $sale->id_address;
    }

    public function LoadToSale($sale)
    {
        $ExistedSaleAnaliticSameAddress = SaleAnaliticsSameAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->one();
     //   echo " <br> загружаем статистику SaleAnaliticsSameAddress";
        $sale->average_price_same = $ExistedSaleAnaliticSameAddress->average_price;
        $sale->average_price_same_count = $ExistedSaleAnaliticSameAddress->average_price_count;
        $sale->radius = $ExistedSaleAnaliticSameAddress->radius;

    }


    // главный метод которые рассчитывает статистику
    public function CalculateStatisticSameAddress()
    {
        // массив радиусов для для поиска похожих вариантов
        $radiuses = [600, 900, 1500, 2000, 3000, 5000];
       // $session = Yii::$app->session;
        // устанавлаиваем адрес с которым работаем
        $address = Addresses::findOne($this->id_address);

        // пробещаемся по этим радиусам от меньшего к большему до наступления общего количества объектов в анализе > 10
        foreach ($radiuses as $radius) {
            $this->radius = $radius;
            // ищем все похожие домиа в радиусах
            $id_addresses_in_radius = $address->getNearestSameIdAddresses($radius, $this->years);


            if (!empty($id_addresses_in_radius)) {
                // подсчитываем количество вариантов с данными id address
                $this->average_price_count = $this->getCounts($id_addresses_in_radius);
                // отладночные данные

                // удаляем откровенные дубликаты которые портят статистику добавляем параметр ->distinct(['id_address', 'phone1'])
                $all_sales_history = $this->getAllUniqueSaleHistoryObjects($id_addresses_in_radius);
                if (count($all_sales_history) < 5) {
                    info("count = ".count($all_sales_history));
                    continue;
                }
                // формируем простой массив с ценами для дальнейшей работы
                $prices = $this->getArrayOfPrices($all_sales_history);

                // if (count($prices) != 0) $average = array_sum($prices) / count($prices);

                // формируем массив реальных цен (вычитаем откровенно бесполезные значения {заниженные и завышенные})
              //  $_SESSION['allPrices'] = $prices;
                $real_prices = $this->DeleteOverPrices($prices, 1, 0.6);
              //  $_SESSION['trimmedPrices'] = $real_prices;
                $count_real_prices = count($real_prices);
                if (($count_real_prices != 0) and ($real_prices)) {

                    $simple_average_price = round(array_sum($real_prices) / count($real_prices));

                    $this->average_price = $simple_average_price;

                    $sales_history = $this->getAllUniqueSaleHistoryObjectsWithPriceLimits($all_sales_history);

                    if ($sales_history) {
                        $this->average_price_count = count($sales_history);
                        $this->ids_sale_history = implode(",", $this->getIdsSaleHistory($sales_history));

                    }

                } else continue;


            }

            if ($this->average_price_count >= 5) break;
        }
        // отладночные данные
      //  $session->setFlash('SaleAnaliticsSameAddress', $this);


        return $this;


    }

// группа вспомогательных методов
    public
    function DeleteOverPrices($prices, $RateUp, $RateDown)
    {

        $count = count($prices);
        $RealPrices = [];
        if (($count != 0) and ($prices)) {
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
          //  $session = Yii::$app->session;
          //  $session->setFlash('RealPrices', "<br>отсортированный массив RealPrices" . implode(",", $RealPrices));

            return $RealPrices;
        } else return false;


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
    function getCounts($id_addresses_in_radius)
    {
        // подсчитываем все количество вариантов с учетом всех параметров
        return Synchronization::find()
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andWhere(['in', 'id_address', $id_addresses_in_radius])
            ->count();
    }

    public
    function getAllUniqueSaleHistoryObjectsOld($id_addresses_in_radius)
    {
        // выбираем уникальные пары phone1 and id_address
        $all_sales_history = Synchronization::find()
            ->select(['phone1', 'id_address'])
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andFilterWhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andfilterWhere(['in', 'id_address', $id_addresses_in_radius])
            ->distinct()
            ->asArray()
            ->all();

        $unique_ids = [];
        if (!$all_sales_history) return false;
        foreach ($all_sales_history as $sale_history) {
            $one_sale_history = Synchronization::find()
                ->Where(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
                ->andFilterWhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
                ->andFilterWhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
                ->andfilterwhere(['id_address' => $sale_history['id_address']])
                ->andfilterwhere(['phone1' => $sale_history['phone1']])
                ->one();
            array_push($unique_ids, $one_sale_history->id);
        }
        $all_sales_history = Synchronization::find()
            // ->distinct('id_address')
            ->Where(['in', 'id', $unique_ids])
            ->orderBy('price')
            ->all();

       // $session->setFlash("CountUniqueAllSaleHistory{$this->radius}", count($all_sales_history));
        return $all_sales_history;
    }

    public
    function getAllUniqueSaleHistoryObjects($id_addresses_in_radius)
    {
        // выбираем уникальные пары id_address, floor
        $all_sales_history = Synchronization::find()
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['<=', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andFilterWhere(['>=', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andfilterWhere(['in', 'id_address', $id_addresses_in_radius])
            ->groupBy( 'id_address,floor')
            ->orderBy('price')
            ->all();


        return $all_sales_history;
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

    public
    function getAllUniqueSaleHistoryObjectsWithPriceLimits($sales_history)
    {
        if (($this->priceMAX) and ($this->priceMAX) and ($this->priceMAX >= $this->priceMIN)) {
            $unique_ids = $this->getIdsSaleHistory($sales_history);
            if (!empty($unique_ids)) $all_sales_history = Synchronization::find()
                ->where(['in', 'id', $unique_ids])
                ->andwhere(['>=', 'price', $this->priceMIN])
                ->andwhere(['<=', 'price', $this->priceMAX])
                ->orderBy('price')
                ->all();
        }
        return $all_sales_history;
    }


    // данный метод формируем из модели Salehistory массив цен ( например $prices = [12133,343434,4343435,6646];)
    public
    function getArrayOfPrices($all_sales_history)
    {
        // формируем массив уникальных ids
        $unique_ids = [];
        if (!$all_sales_history) return false;
        foreach ($all_sales_history as $item) {
            array_push($unique_ids, $item['id']);
        }

        // выбираем цены из уникальных ids
        $pricesFull = Synchronization::find()
            ->select('price')
            ->Where(['in', 'id', $unique_ids])
            ->orderBy('price')
            ->all();

        $prices = [];

        // формируем массив с ценами
        foreach ($pricesFull as $item) {
            array_push($prices, (int)$item['price']);
        }

        // отладночные данные
   //     $session = Yii::$app->session;
      //  $session->setFlash('prices', "<br>отсортированный массив prices" . implode(",", $prices));


        return $prices;
    }



}
