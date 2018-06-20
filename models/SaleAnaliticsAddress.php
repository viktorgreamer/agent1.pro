<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "sale_analitics_address".
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
 * @property integer $date время расчета аналитики
 */
class SaleAnaliticsAddress extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_analitics_address';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_analitics_address";
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
            [['id_address', 'rooms_count', 'grossarea'], 'required'],
            [['id_address', 'rooms_count', 'grossarea', 'average_price', 'average_price_count', 'house_type', 'floorcount', 'year', 'type_of_plan'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'id_address' => 'Id Address',
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
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();

        //if ($isExists) echo " <br>!!! данная статистика SaleAnaliticsAddress существует";

        return $isExists;


    }

    // проставляем параметры в объект SaleAnaliticsAddress
    public function ExportParametersFromSale($sale)
    {
        // $sale - объект $sale, который пошел на анализ

        if ($this->percent == 0) $this->percent = 5;
        if ($this->period == 0) $this->period = 6;
        $this->rooms_count = $sale->rooms_count;
        $this->grossarea = round($sale->grossarea);
        $this->id_address = $sale->id_address;


    }

    public function LoadToSale($sale)
    {
        $ExistedSaleAnaliticsAddress = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->one();
       // echo " <br> загружаем статистику SaleAnaliticsAddress";

        $sale->average_price_address = $ExistedSaleAnaliticsAddress->average_price;
        $sale->average_price_address_count = $ExistedSaleAnaliticsAddress->average_price_count;

    }

    // главный метод которые рассчитывает статистику
    public function CalculateStatisticAddress()
    {

      //  $session = Yii::$app->session;
        // устанавлаиваем адрес с которым работаем
        $address = Addresses::findOne($this->id_address);

        // подсчитываем количество вариантов с данными id address
        $this->average_price_count = $this->getCounts();
        // отладночные данные
      //  $session->setFlash('average_price_count', "<br>количество вариантов с данными id address" . $this->average_price_count);

        // удаляем откровенные дубликаты которые портят статистику добавляем параметр ->distinct(['id_address', 'phone1'])
        $all_sales_history = $this->getAllUniqueSaleHistoryObjects();

        // формируем простой массив с ценами для дальнейшей работы
        $prices = $this->getArrayOfPrices($all_sales_history);

        // if (count($prices) != 0) $average = array_sum($prices) / count($prices);

        // формируем массив реальных цен (вычитаем откровенно бесполезные значения {заниженные и завышенные})
        $real_prices = $this->DeleteOverPrices($prices, 1.1, 0.6);

        $count_real_prices = count($real_prices);
        if ($count_real_prices != 0) {

            $simple_average_price = round(array_sum($real_prices) / count($real_prices));

            $this->average_price = $simple_average_price;

            //берем sale_history которые попадают в лимиты
            $sales_history = $this->getAllUniqueSaleHistoryObjectsWithPriceLimits($all_sales_history);

            // догружаем параметры
            $this->average_price_count = count($sales_history);
            $this->ids_sale_history = implode(",", $this->getIdsSaleHistory($sales_history));


        }


        // отладночные данные
      //  $session->setFlash('SaleAnaliticsAddress', $this);


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
           // $session = Yii::$app->session;
        //    $session->setFlash('RealPrices', "<br>отсортированный массив RealPrices" . implode(",", $RealPrices));

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
            ->andWhere(['id_address' => $this->id_address])
            ->count();
    }

    public
    function getAllUniqueSaleHistoryObjects()
    {
        // выбираем уникальные пары phone1 и floor по данному id_address
        $all_sales_history = Synchronization::find()
            ->distinct()
            ->select(['phone1', 'floor'])
            ->asArray()
            ->Where(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
            ->andFilterWhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
            ->andWhere(['id_address' => $this->id_address])
            ->all();
      //  $session = Yii::$app->session;
      //  $session->setFlash("CountAllSaleHistory", (int)$this->getCounts());
        if (!$all_sales_history) return false;
        $unique_ids = [];
        foreach ($all_sales_history as $sale_history) {
            // выбираем по одной записи каждого из уникального
            $one_sale_history = Synchronization::find()
                ->Where(['rooms_count' => $this->rooms_count])
                ->andFilterWhere(['>', 'date_start', (time() - $this->period * 30 * 24 * 60 * 60)])
                ->andFilterWhere(['<', 'grossarea', ($this->grossarea * (100 + $this->percent) / 100)])
                ->andFilterWhere(['>', 'grossarea', ($this->grossarea * (100 - $this->percent) / 100)])
                ->andfilterwhere(['floor' => $sale_history['floor']])
                ->andfilterwhere(['phone1' => $sale_history['phone1']])
                ->andWhere(['id_address' => $this->id_address])
                ->orderBy('price')
                ->one();
            array_push($unique_ids, $one_sale_history->id);
        }
        $all_sales_history = Synchronization::find()
            // ->distinct('id_address')
            ->Where(['in', 'id', $unique_ids])
            ->orderBy('price')
            ->all();


       // $session->setFlash("CountUniqueAllSaleHistory", count($all_sales_history));
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

        $all_sales_history = Synchronization::find()
            ->andWhere(['in', 'id', $this->getIdsSaleHistory($sales_history)])
            ->andwhere(['between', 'price', $this->priceMIN, $this->priceMAX])
            ->orderBy('price')
            ->all();

       // $session = Yii::$app->session;
//
     //   $session->setFlash("CountAllUniqueSaleHistoryWithPriceLimits", count($all_sales_history));

        return $all_sales_history;
    }


// данный метод формируем из модели Salehistory массив цен ( например $prices = [12133,343434,4343435,6646];)
    public
    function getArrayOfPrices($all_sales_history)
    {
        // формируем массив уникальных ids
        $unique_ids = [];
        if ($all_sales_history) {
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
           // $session = Yii::$app->session;
         //   $session->setFlash('prices', "<br>отсортированный массив prices" . implode(",", $prices));


        }


        return $prices;
    }

    public
    function DeleteFullDublicates($all_sales_history)
    {
        $new_all_sales_history = $all_sales_history;
        $i = 0;
        $marked_ids = [];
        // удаляем из массива дабуликаты по адресу и телефону
        foreach ($all_sales_history as $sale_history) {
            foreach ($new_all_sales_history as $new_sale_history) {
                //если количество повторений равно более 1 то это явный дубликат
                if (($sale_history['phone1'] == $new_sale_history['phone1']) and ($sale_history['id_address'] == $new_sale_history['id_address'])
                    and ($sale_history['id'] != $new_sale_history['id']) and (!in_array($new_sale_history['id'], $marked_ids))
                ) {
                    unset($all_sales_history[$i]);
                    echo " удалили явный дубликат" . $sale_history['id'] . " и " . $new_sale_history['id'];
                    array_push($marked_ids, $new_sale_history['id']);
                }
            }
            $i++;

        }


    }


}
