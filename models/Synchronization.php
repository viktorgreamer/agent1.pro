<?php

namespace app\models;

use app\models\ParsingModels\ParsingSync;
use app\models\SaleFilters;
use app\components\SaleWidget;
use app\models\Tags;
use yii\helpers\ArrayHelper;


use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "{{%Velikiy_Novgorod_synchronization}}".
 *
 * @property integer $id
 * @property integer $id_source
 * @property integer $id_in_source
 * @property string $link
 * @property string $title
 * @property string $tags_id
 * @property string $address
 * @property string $patterns
 * @property string $minus_patterns
 * @property integer $is_active
 * @property integer $price
 * @property integer $date_of_check
 * @property integer $date_of_die
 */
class Synchronization extends Sale
{

    /**
     * @inheritdoc
     */
    public static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_synchronization';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_synchronization";
        }
    }

    public static function setTablePrefix($prefix)
    {
        self::$tablePrefix = $prefix;
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    public function getSimilar_ids()
    {
        $ids_sale = Synchronization::find()
            ->distinct('id')
            ->select('id')
            ->where(['floor' => $this->floor])
            ->andwhere(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            // ->andwhere(['<>', 'id_address', null])
            ->andWhere(['not in', 'disactive', [1, 2]])
            ->andWhere(['<>', 'id', $this->id])
            ->andWhere(['>', 'grossarea', ($this->grossarea * 0.90)])
            ->andWhere(['<', 'grossarea', ($this->grossarea * 1.1)])
            ->orderBy('price')
            ->all();

        if ($ids_sale) {
            $ids = [];
            foreach ($ids_sale as $item) {
                array_push($ids, $item->id);
            }
            //  info("есть похожие" . implode(",", $ids), 'success');


            $this->similar_ids = implode(",", $ids);
            array_push($ids, $this->id);
            foreach ($ids as $id) {
                $sale = Synchronization::findOne($id);
                $sale->similar_ids = implode(",", MyArrayHelpers::DeleteFromArray($id, $ids));
                //  echo "<br> the same ids" . $sale->similar_ids;
                $sale->save();
            }
        } else {
            $this->similar_ids = '';
            //  info("нет похожих" . $this->id_address . " > " . $this->rooms_count . " > " . $this->address . " ", 'alert');
            return false;
        }


    }

    public function getSimilar()
    {
        return $this->hasOne(SaleSimilar::className(), ['id_similar' => 'id']);

    }


    public function RenderFloors()
    {
        return $this->floor . "/" . $this->floorcount;
    }

    public function SalefiltersCheck()
    {
        $salefilters = SaleFilters::find()->where(['mail_inform' => 1])->all();
        //  if (Yii::$app->controller->id == 'console') echo " число фильтров для информирования по email" . count($salefilters);
        if ($salefilters) {
            foreach ($salefilters as $salefilter) {
                // тестовый режим
                $response = $salefilter->Is_in_salefilter($this);
                if ($response) {
                    //    echo "<h1> YES i am in sale filter</h1>";
                    // Yii::$app->mailer->htmlLayout = '@app/views/layouts/mail';
                    // '@app/views/sale/_sale-table', ['model' => $this]
                    Yii::$app->mailer->compose()
                        ->setTo('an.viktory@gmail.com')
                        ->setFrom(['viktorgreamer1@yandex.ru' => 'agent1.pro'])
                        ->setSubject("По фильтру:" . " " . $salefilter->name)
                        ->setTextBody($response . " " . $this->url)
                        ->send();
                } // else  echo "<h1> NO i am in sale filter</h1>";


            }
        }
    }

    /*
     * @var $salefilter SaleFilters
     * проверяет подходит ли этот вариант под параметры какого-нибудь фильтра аггрегатора
     * */
    public function AggregatorsCheck()
    {
        // ищем листы которые имеют свойство аггрегатора
        $salelists = SaleLists::find()->where(['type' => 3])->all();

        $ids = []; // массив id salefilters, которые является родителями этих листов
        foreach ($salelists as $salelist) {
            echo " <h4>" . $salelist->name . "</h4>";
            $lists_of_ids = explode(",", $salelist->list_of_ids);
            $ids_ok = explode(",", $salelist->ids_ok);
            $ids_ban = explode(",", $salelist->ids_ban);
            $ids_common = $lists_of_ids + $ids_ok + $ids_ban;
            $salefilters = SaleFilters::find()->where(['in', 'id', explode(",", $salelist->parent_salefilter)])->all();
            // пробегаемся по всем фильтрам родителям данного списка аггрегатора
            if ($salefilters) {
                foreach ($salefilters as $salefilter) {
                    echo " <h5 class='success-color'>" . $salefilter->name . "</h5>";
                    if (($salefilter->Is_in_salefilter($this)) and (!in_array($this->id, $ids_common))) {

                        array_push($lists_of_ids, $this->id);

                        echo "<h3 class='success-color'>  i am in sale filter by AggtegatorsCheck</h3>";
                    } else echo "<h4> NO i am not in any filter</h4>";


                }
            }
            $salelist->list_of_ids = implode(",", $lists_of_ids);
            if (!$salelist->save()) my_var_dump($salelist->getErrors());
        }
    }

    /**
     * @inheritdoc
     */

    public
    function rules()
    {
        return [
            // [['id'], 'required'],
            [['id', 'date_start', 'rooms_count', 'price', 'id_address', 'house_type', 'floor', 'floorcount', 'id_sources', 'year',
                'load_analized', 'parsed', 'geocodated', 'sync', 'disactive', 'moderated', 'processed', 'status_unique_date', 'status_blacklist2', 'average_price', 'average_price_count', 'average_price_address', 'average_price_address_count', 'average_price_same', 'average_price_same_count'], 'integer'],
            [['coords_x', 'coords_y'], 'number'],
            // [['grossarea', 'living_area', 'kitchen_area'], 'number'],
            [['description', 'images', 'url', 'person', 'id_irr_duplicate', 'tags_id'], 'string'],
            [['title', 'phone1', 'phone2', 'city', 'address', 'locality', 'address_line'], 'string', 'max' => 255],
            [['id_in_source'], 'string', 'max' => 40],
            [['tags_id'], 'string', 'max' => 1000],
            [['id'], 'unique'],
        ];
    }


    /**
     * @inheritdoc
     */
    public
    static function getDb()
    {
        return Yii::$app->cloud;
    }

    public function init()
    {   //приведение параметров модели в исходное состояние
        // не парсилось
        $this->parsed = 1;
        // не геокодировалось
        $this->geocodated = 5;
        // не обрабатывалась
        $this->processed = 1;
        // анализ не проводился
        $this->load_analized = 1;
        $this->price = 0;
        // автозагрузка тегов не производилась
        $this->tags_autoload = 1;
        // синхронизация с web сервером не производилась
        $this->sync = 1;
        // модерация не производилась
        $this->moderated = 1;
        // телефон вставляем пустым
        $this->phone1 = '';
        // основной статус
        $this->status = 3;
        $this->id_similar = 0;


        $this->disactive = 0;

        parent::init(); // TODO: Change the autogenerated stub
    }

    /**
     * @inheritdoc
     */
    public
    function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sources' => 'Ресурс',
            'id_in_source' => 'Id в ресурсе',
            'url' => 'Ссылка',
            'title' => 'Title',
            'address' => 'Address',
            'disactive' => 'Активность',
            'status' => 'Cтатус',
            'price' => 'Price',
            'rooms_count' => 'Кол-во комнат',
            'load_analized' => 'Статус Анализа',
            'parsed' => 'Статус Парсинга',
            'geocodated' => 'Статус Геокодирования',
            'sync' => 'Статус Синхронизации',
            'moderated' => 'Статус Модерации',
            'processed' => 'Статус Обработки',
            'tags_autoload' => 'Статус tags_autoload',
        ];
    }

    /**
     * @inheritdoc
     * создание нового объекта из ответа
     */
    public
    function LoadNew($response)
    {
        $this->title = $response['title'];
        $this->address_line = $response['address_line'];
        $this->id_sources = ParsingExtractionMethods::getSourceIdFromUrl($response['url']);
        $this->id_in_source = $response['id_in_source'];
        $this->price = $response['price'];
        $this->url = $response['url'];
        $this->date_start = $response['date_start'];

    }


    public function getAddresses()
    {
        {
            return $this->hasOne(Addresses::className(), ['id' => 'id_address']);
        }

    }

    /**
     * @inheritdoc
     */
    public
    static function TODO($response, $id_category = null)
    {
        // поиск, что данный id уже есть в базе
        $active_item = Synchronization::find()
            ->select('id,id_address, id_in_source, address_line, price,date_of_check,url,date_start,disactive')
            ->where(['id_in_source' => $response['id']])
            ->andwhere(['id_sources' => $response['id_sources']])
            ->one();
        // echo "<br>".$active_item->addresses->address;

        // если такой уже есть то делаем update или ничего не делаем
        //  if ($active_item->id != 4342) return 'JKJ';
        if ($active_item) {

            // делаем update
            $return = '';
            // если ADDRESS_CHANGED
            if (trim($active_item->address_line) != $response['address_line']) {
                if ($active_item->id_address) {
                    $address = Addresses::findOne($active_item->id_address);
                    if ($address) {
                        $pattern = $address->getPattern();
                        if ($pattern) {
                            if (!preg_match($address->getPattern() . "iu", $response['address_line'], $output)) {
                                $log = [5, time(), trim($active_item->address_line), $response['address_line']];
                                echo "<br>" . Sale::RenderOneLog($log);
                                $active_item->UpdateAddress($response, $log);
                                info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');
                                $return .= "ADDRESS_CHANGED";
                                //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);

                            };
                        } else {
                            $log = [5, time(), trim($active_item->address_line), $response['address_line']];
                            echo "<br>" . Sale::RenderOneLog($log);
                            $active_item->UpdateAddress($response, $log);
                            info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');
                            $return .= "ADDRESS_CHANGED";
                            //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);
                        }
                    }


                } else {
                    $log = [5, time(), trim($active_item->address_line), $response['address_line']];
                    echo "<br>" . Sale::RenderOneLog($log);
                    $active_item->UpdateAddress($response, $log);
                    info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');

                    $return .= "ADDRESS_CHANGED";
                    //  Renders::SystemMail(" ADDRESS_CHANGED WITHOUT ID ADDRESS ID=".$active_item->id);
                }


            }
            // если PRICE_CHANGED
            if ($active_item->price != $response['price']) {
                $log = [4, time(), $active_item->price, $response['price']];
                $active_item->price = $response['price'];
                $active_item->addLog($log);
                echo "<br>" . Sale::RenderOneLog($log);
                info("id=" . $active_item->id . " " . $active_item->changingStatuses('PRICE_CHANGED'), 'alert');
                $return .= "PRICE_CHANGED";

            }

            $delay = time() - $active_item->date_of_check;
            // если THE_SAME
            if ($delay > 100) {
                // echo "<br> !!!объект остался прежним";
                if ($response['date_start'] != $active_item->date_start) {
                    $log = [7, time(), date("d.m.y H:i:s", $active_item->date_start), date("d.m.y H:i:s", $response['date_start'])];
                    echo "<br>" . Sale::RenderOneLog($log);
                    $active_item->changingStatuses('DATESTART_UPDATED');
                    $return .= 'DATESTART_UPDATED';
                    $active_item->date_start = $response['date_start'];
                    $active_item->addLog($log);
                } else {
                    $active_item->changingStatuses('THE_SAME');
                    $return .= ' THE_SAME';
                }

                // значит ничего не изменилось

            } else  echo "<br> объект проверяли " . $delay . " секунд назад";


            // TODO ОТМЕНИТЬ КОГДА ВСЕ СВЕРИМ
            // $active_item->address_line = $response['address_line'];
            //  $active_item->price = $response['price'];
            // обновляем время проверки
            $active_item->date_of_check = time();
            $active_item->id_category = $id_category;

            //  if ($active_item->id_in_source == '1036149207') info("1036149207 DISACTIVE=".$active_item->disactive, 'alert');
            //  if ($return) echo "<br>СОХРАНЯЕМ ИЗМЕНЕННЫЕ СТАТУСЫ : " . $return . " ЦЕНА" . $active_item->price;

            if (!$active_item->save()) my_var_dump($active_item->getErrors());
            return $return;
        } else {
            // создаем новый объект и загружаем в него свойства
            $active_item = New Synchronization();
            $active_item->id_category = $id_category;
            $active_item->LoadNew($response);
            info($active_item->changingStatuses("NEW"), 'alert');
            $active_item->date_of_check = time();
            $active_item->original_date = null;
            // echo "<br>" . $response['url'] . " новый объект";

            if (!$active_item->save()) my_var_dump($active_item->getErrors());
            return " NEW";
        }
    }

    public static function getActiveItemFromBase($parsing)
    {
        // поиск, что данный id уже есть в базе
        $active_item = Synchronization::find()
            ->select('id,id_address, id_in_source, address_line, price,date_of_check,url,date_start,disactive')
            ->where(['id_in_source' => $parsing['id_in_source']])
            ->andwhere(['id_sources' => $parsing['id_sources']])
            ->one();
        if ($active_item) info("GET ONE ITEM FROM BASE", WARNING);
    }

    public static function getExistedItem($parsing, $config)
    {
        $active_items = Synchronization::getCachedCategory($config->id);
        $active_item = $active_items['id_in_source'];
        if (!$active_item) {

            $active_item = Synchronization::getActiveItemFromBase($parsing);
        } else {
          //  info("GET ONE ITEM FROM FROM CASH", SUCCESS);
        }

        return $active_item;
    }

    public
    static function TODO_NEW($parsing, $config, $options = [])
    {
        $manual_save = false;
        /* @var $active_item Sale */
        $active_item = $options['active_item'];
        if (!$active_item) {
         //   info("TAKING ITEM FROM BASE", WARNING);

            $active_item = Synchronization::getActiveItemFromBase($parsing);
            $manual_save = true;
        } else {
          //  info("TAKING ITEM FROM CASHE", SUCCESS);

        }

        // echo "<br>".$active_item->addresses->address;
        // если такой уже есть то делаем update или ничего не делаем
        //  if ($active_item->id != 4342) return 'JKJ';
        if ($active_item) {
            info("SYNC PROCESSING ...", PRIMARY);

            // делаем update
            $return = '';
            // если ADDRESS_CHANGED
            if (trim($active_item->address_line) != $parsing['address_line']) {
                if ($active_item->id_address) {
                    $address = Addresses::findOne($active_item->id_address);
                    if ($address) {
                        $pattern = $address->getPattern();
                        if ($pattern) {
                            if (!preg_match($address->getPattern() . "iu", $parsing['address_line'], $output)) {
                                $log = [5, time(), trim($active_item->address_line), $parsing['address_line']];
                                info(Sale::RenderOneLog($log));
                                $active_item->UpdateAddress($parsing, $log);
                                info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), DANGER);
                                $return .= "ADDRESS_CHANGED";
                                //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);

                            };
                        } else {
                            $log = [5, time(), trim($active_item->address_line), $parsing['address_line']];
                            info(Sale::RenderOneLog($log));
                            $active_item->UpdateAddress($parsing, $log);
                            info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), DANGER);
                            $return .= "ADDRESS_CHANGED";
                            //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);
                        }
                    }


                } else {
                    $log = [5, time(), trim($active_item->address_line), $parsing['address_line']];
                    echo "<br>" . Sale::RenderOneLog($log);
                    $active_item->UpdateAddress($parsing, $log);
                    info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), DANGER);

                    $return .= "ADDRESS_CHANGED";
                    //  Renders::SystemMail(" ADDRESS_CHANGED WITHOUT ID ADDRESS ID=".$active_item->id);
                }


            }
            // если PRICE_CHANGED
            if ($active_item->price != $parsing['price']) {
                $log = [4, time(), $active_item->price, $parsing['price']];
                $active_item->price = $parsing['price'];
                $active_item->addLog($log);
                echo "<br>" . Sale::RenderOneLog($log);
                info("id=" . $active_item->id . " " . $active_item->changingStatuses('PRICE_CHANGED'), DANGER);
                $return .= "PRICE_CHANGED";

            }

            $delay = time() - $active_item->date_of_check;
            // если THE_SAME
            if ($delay > 100) {
                // echo "<br> !!!объект остался прежним";
                if ($parsing['date_start'] != $active_item->date_start) {
                    $log = [7, time(), date("d.m.y H:i:s", $active_item->date_start), date("d.m.y H:i:s", $parsing['date_start'])];
                    info(Sale::RenderOneLog($log));
                    $active_item->changingStatuses('DATESTART_UPDATED');
                    $return .= 'DATESTART_UPDATED';
                    $active_item->date_start = $parsing['date_start'];
                    $active_item->addLog($log);
                } else {
                    info($active_item->changingStatuses('THE_SAME'), PRIMARY);
                    $return .= ' THE_SAME';
                }

                // значит ничего не изменилось

            } else  echo info(" object was checked " . $delay . " seconds ago");

            // TODO ОТМЕНИТЬ КОГДА ВСЕ СВЕРИМ
            // $active_item->address_line = $parsing['address_line'];
            //  $active_item->price = $parsing['price'];
            // обновляем время проверки


            if ((!$active_item->id_category) OR (!preg_match("/THE_SAME/", $return)) OR ($manual_save)) {
                my_var_dump($active_item->id_category);
                my_var_dump($return);
                my_var_dump($manual_save);
                info("SAVING SINGLE ITEM BECAUSE IT HAS CHANGES", SUCCESS);
                $active_item->date_of_check = time();
                $active_item->id_category = $config->id;
                if (!$active_item->save()) my_var_dump($active_item->getErrors());
            }

            return $return;
        } else {
            info("CREATING NEW ...", PRIMARY);

            // создаем новый объект и загружаем в него свойства
            $active_item = New Synchronization();
            $active_item->id_category = $config->id;
            $active_item->LoadNew($parsing);
            info($active_item->changingStatuses("NEW"), DANGER);
            $active_item->date_of_check = time();
            $active_item->original_date = time();
            // echo "<br>" . $parsing['url'] . " новый объект";

            if (!$active_item->save()) my_var_dump($active_item->getErrors());
            Notifications::VKMessage($active_item->id);

            return " NEW";
        }
    }


    public static function getCachedCategory($id_category)
    {
        $cashed_items = Yii::$app->cache->get("CACHED_ID_CATEGORY_" . $id_category);
        if (!$cashed_items) {
            info(" GET DATA FROM BASE", WARNING);
            $cashed_items = Synchronization::find()
                ->select('id,id_address, id_in_source, address_line, price,date_of_check,url,date_start,disactive,id_category')
                ->where(['id_category' => $id_category])
                // ->asArray()
                ->indexBy('id_in_source')
                ->all();
            Yii::$app->cache->set("CACHED_ID_CATEGORY_" . $id_category, $cashed_items, 50);

        } else {
            info(" GET CASHED DATA CONFIGURATION", SUCCESS);
        }
        //  my_var_dump($cashed_items);
        return $cashed_items;
    }

    public
    static function TODO_NEW1($response, $active_item)
    {

        // делаем update
        $return = '';
        // если ADDRESS_CHANGED
        if (trim($active_item['address_line']) != $response['address_line']) {
            if ($active_item['id_address']) {
                $address = Addresses::findOne($active_item['id_address']);
                if ($address) {
                    $pattern = $address->getPattern();
                    if ($pattern) {
                        if (!preg_match($address->getPattern() . "iu", $response['address_line'], $output)) {
                            $log = [5, time(), trim($active_item['address_line'], $response['address_line'])];
                            echo "<br>" . Sale::RenderOneLog($log);
                            $active_item->UpdateAddress($response, $log);
                            $active_item = Synchronization::findOne($active_item['id']);
                            info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');
                            $return .= "ADDRESS_CHANGED";
                            //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);

                        };
                    } else {
                        $log = [5, time(), trim($active_item['address_line']), $response['address_line']];
                        $active_item = Synchronization::findOne($active_item['id']);
                        echo "<br>" . Sale::RenderOneLog($log);
                        $active_item->UpdateAddress($response, $log);

                        info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');
                        $return .= "ADDRESS_CHANGED";
                        //  Renders::SystemMail(" pattern ='".$address->getPattern()."' ADDRESS_CHANGED ID=".$active_item->id);
                    }
                }


            } else {
                $log = [5, time(), trim($active_item['address_line']), $response['address_line']];
                echo "<br>" . Sale::RenderOneLog($log);
                $active_item = Synchronization::findOne($active_item['id']);
                $active_item->UpdateAddress($response, $log);
                info("id=" . $active_item->id . " " . $active_item->changingStatuses('ADDRESS_CHANGED'), 'alert');

                $return .= "ADDRESS_CHANGED";
                //  Renders::SystemMail(" ADDRESS_CHANGED WITHOUT ID ADDRESS ID=".$active_item->id);
            }


        }
        // если PRICE_CHANGED
        if ($active_item['price'] != $response['price']) {
            $active_item = Synchronization::findOne($active_item['id']);
            $log = [4, time(), $active_item->price, $response['price']];
            $active_item->price = $response['price'];
            $active_item->addLog($log);
            echo "<br>" . Sale::RenderOneLog($log);
            info("id=" . $active_item->id . " " . $active_item->changingStatuses('PRICE_CHANGED'), 'alert');
            $return .= "PRICE_CHANGED";

        }

        $delay = time() - $active_item->date_of_check;
        // если THE_SAME
        if ($delay > 100) {
            // echo "<br> !!!объект остался прежним";
            if ($response['date_start'] != $active_item['date_start']) {
                $active_item = Synchronization::findOne($active_item['id']);

                $log = [7, time(), date("d.m.y H:i:s", $active_item->date_start), date("d.m.y H:i:s", $response['date_start'])];
                echo "<br>" . Sale::RenderOneLog($log);
                $active_item->changingStatuses('DATESTART_UPDATED');
                $return .= 'DATESTART_UPDATED';
                $active_item->date_start = $response['date_start'];
                $active_item->addLog($log);
            } else {

                Synchronization::updateAll(['disactive' => 0, 'date_of_check' => time()], ['id' => $active_item['id']]);
                $active_item->changingStatuses('THE_SAME');
                $return .= ' THE_SAME';
            }

            // значит ничего не изменилось

        } else  echo "<br> объект проверяли " . $delay . " секунд назад";


        // TODO ОТМЕНИТЬ КОГДА ВСЕ СВЕРИМ
        // $active_item->address_line = $response['address_line'];
        //  $active_item->price = $response['price'];
        // обновляем время проверки
        return $return;

    }

//    public function similarCheck()
//    {
//        info("similarCheck");
//        $similar_ids = $this->searchSimilar_main();
////        $public_similar_ids = $this->searchPublicSimilar_main();
////        info("SALE_SIMILARS = '" . my_implode($similar_ids) . "'" . " count = " . count($similar_ids), 'alert');
////        info("SALE_SIMILARS_public = '" . my_implode($public_similar_ids) . "'" . " count = " . count($public_similar_ids), 'alert');
//////        $similar_sales = Synchronization::find()
//////            ->where(['in', 'id', $public_similar_ids])
//////            ->orderBy('price')
//////            ->all();
//////       // echo Renders::StaticView('_chart_prices', ['prices' => $this->searchSimilar('s.price'), 'labels' => $this->searchSimilar('s.id') ]);
////        // echo Renders::StaticView('mini_sale_similar', ['sales' => $similar_sales, 'contacts' => true, 'controls' => true]);
////        $this->createSimilar($public_similar_ids, $similar_ids);
//    }

    /*
    метод для подсчета количества записей определенного параметра и его статусов
    /* @var
    */
    public
    static function Counts($startQuery, $params_and_status)
    {
        // my_var_dump($params_and_status);
        foreach ($params_and_status as $param => $statuses) {
            echo "<p class='h3'>" . Synchronization::attributeLabels()[$param] . " :</p>";
            // берем названия каждого статуса
            $names_of_status = Sale::STATUS_NAMES[$param];

            foreach ($statuses as $status) {
                $query = clone $startQuery;
                $count = $query->andwhere([$param => $status])->count();
                echo $names_of_status[$status] . " <span class='badge badge-success'>" . $count . "</span> ";

            }
            echo "<hr>";
        }


    }

    /*
        метод для рендеринга статусов
        */
    public
    function renderStatuses($params = ['disactive', 'parsed', 'geocodated', 'load_analized', 'processed', 'sync', 'moderated'])
    {
        foreach ($params as $param) {
            $names_of_status = Sale::STATUS_NAMES[$param];
            if (in_array($names_of_status[$this[$param]], ['READY'])) echo " " . $param . " = <span class='badge badge-success'>" . $names_of_status[$this[$param]] . "</span> ";
            elseif ($names_of_status[$this[$param]] == 'NOT') echo " " . $param . " = <span class='badge badge-danger'>" . $names_of_status[$this[$param]] . "</span> ";
            elseif (in_array($names_of_status[$this[$param]], ['DONE', 'FULL EXACT', 'MAN'])) echo " " . $param . " = <span class='badge badge-primary'>" . $names_of_status[$this[$param]] . "</span> ";
            elseif (in_array($names_of_status[$this[$param]], ['DENIED', 'ERR'])) echo " " . $param . " = <span class='badge black'>" . $names_of_status[$this[$param]] . "</span> ";
            else echo " " . $param . " = <span class='badge badge-default'>" . $names_of_status[$this[$param]] . "</span> ";
        }
    }


    /*
    данные метод  проверяет все потерянные варианты (удаленные или истекщие) с момента последней синхронизации
    */
    public
    static function CheckLostLinks()
    {
        $module = yii::$app->params['module'];
        $n = 0;
        $lost_items = Synchronization::find()->where(['<', 'date_of_check', ParsingConfiguration::LAST_TIMESTAMP($module->id) - ParsingConfiguration::PERIOD_OF_CHECK_LOST_LINKS * 60 * 60])->andWhere(['not in', 'disactive', [1, 2]])->all();
        if ($lost_items) {
            foreach ($lost_items as $lost_item) {
                $n++;
                echo "<br> item id=" . Html::a($lost_item->url, $lost_item->url, ['target' => '_blank']);
                // ставим что она неактивна
                $lost_item->changingStatuses("DISABLED");

                $lost_item->date_of_die = time();
                $lost_item->save();
            }
        }
        return "<br> <h4> lost </h4>" . $n . " objects";
    }

    public
    static function CheckDiedLinks()
    {
        $module = yii::$app->params['module'];
        $n = 0;
        $lost_items = Synchronization::find()->where(['<', 'date_of_check', ParsingConfiguration::LAST_TIMESTAMP($module->id) - ParsingConfiguration::PERIOD_CHECK_OF_DEATH * 60 * 60])->andWhere(['<>', 'disactive', 1])->all();
        if ($lost_items) {
            foreach ($lost_items as $lost_item) {
                $n++;
                echo "<br> item id=" . $lost_item->url . " died";
                // ставим что она неактивна
                $lost_item->changingStatuses('DELETED');
                $lost_item->date_of_die = time();
                $lost_item->save();
            }
        }
        return "<br> <h4> died </h4>" . $n . " objects";
    }

    public
    function updateSaleStatus($status)
    {
        $sale = Sale::find()
            ->where(['id_in_source' => $this->id_in_source])->one();
        if ($sale) {
            $sale->disactive = $status;
            $sale->save();
        }
    }

    public
    function updateInCloud()
    {
        switch ($this->disactive) {

            case 1:
                echo "i равно 1";
                break;
            case 2:
                echo "i равно 2";
                break;
            default:
                echo "i не равно 0, 1 или 2";
        }
        $updated = Parsing::findOne($this->id);
        $updated->url = $this->url;

    }

    protected
    function addLog($log)
    {
        $new_log = New SaleLog();
        $new_log->sale_id = $this->id;
        $new_log->type = $log[0];
        $new_log->date = time();
        $new_log->was = (string)$log[2];
        $new_log->now = (string)$log[3];
        if (!$new_log->save()) my_var_dump($new_log->getErrors());

    }

    public
    function UpdatePrice($response, $log)
    {

        $this->price = $response['price'];

        $this->addLog($log);

    }

    public
    function UpdateAddress($response, $log)
    {
        $this->address_line = $response['address_line'];

        $this->url = $response['url'];
        $this->addLog($log);


    }

    public
    function LoadStatistic()
    {
        // SaleAnalitics
        $SaleAnalitics = New SaleAnalitics();
        $SaleAnalitics->ExportParametersFromSale($this);
        if (!$SaleAnalitics->IsExists()) {
            $SaleAnalitics->CalculateStatistic();
            if ($SaleAnalitics->average_price) {
                if (!$SaleAnalitics->save()) my_var_dump($SaleAnalitics->getErrors());
            }

        }
        $SaleAnalitics->LoadToSale($this);

        // SaleAnaliticsAddress
        $SaleAnaliticsAddress = New SaleAnaliticsAddress();
        $SaleAnaliticsAddress->ExportParametersFromSale($this);
        if (!$SaleAnaliticsAddress->IsExists()) {
            $SaleAnaliticsAddress->CalculateStatisticAddress();
            if ($SaleAnaliticsAddress->average_price) {
                if (!$SaleAnaliticsAddress->save()) my_var_dump($SaleAnaliticsAddress->getErrors());
            }

        }
        $SaleAnaliticsAddress->LoadToSale($this);

        // SaleAnaliticsSameAddress
        $SaleAnaliticsSameAddress = New SaleAnaliticsSameAddress();
        $SaleAnaliticsSameAddress->ExportParametersFromSale($this);
        if (!$SaleAnaliticsSameAddress->IsExists()) {
            $SaleAnaliticsSameAddress->CalculateStatisticSameAddress();
            if ($SaleAnaliticsSameAddress->average_price) {
                if (!$SaleAnaliticsSameAddress->save()) my_var_dump($SaleAnaliticsSameAddress->getErrors());
            }

        }
        $SaleAnaliticsSameAddress->LoadToSale($this);


    }

    public
    function checkForAgents()
    {


        $agent = Agents::find()->where(['phone' => $this->phone1])->andWhere(['person_type' => 1])->one();
        if ($agent) {
            // делаем update количество обьявлений
            if ($this->id_sources == 1) $agent->irr_count = $agent->irr_count + 1;
            if ($this->id_sources == 2) $agent->yandex_count = $agent->yandex_count + 1;
            if ($this->id_sources == 3) $agent->avito_count = $agent->avito_count + 1;
            if ($this->id_sources == 4) $agent->youla_count = $agent->youla_count + 1;
            if ($this->id_sources == 5) $agent->cian_count = $agent->cian_count + 1;
            $agent->count_ads = $agent->count_ads + 1;
            info("Прибавили count_ads +1 стало " . $agent->count_ads);
            $agent->save();
            //  echo "<br> yes, i've found the " . $agent->person . " phone1=" . $this->phone1;
            $this->status_blacklist2 = $agent->person_type;
            $this->person = $agent->person;
        } else {
            info('нет в списке агентов');
            // если не нашли такого агента то ищем количество объявлекний с таким номером в истории
            $counts_phone1_in_salehistory = Synchronization::find()->where(['phone1' => $this->phone1])->count();
            info(" counts_phone1_in_salehistory " . $counts_phone1_in_salehistory);
            // если их больше двух то заносим в базу Agents
            $agent = new Agents();
            $agent->phone = $this->phone1;
            $agent->person = $this->person;
            // ищем количество объявлений в истории
            $agent->irr_count = Synchronization::find()->where(['id_sources' => 1])->andWhere(['>', 'date_start', time() - 2 * 30 * 24 * 60 * 60])->andWhere(['phone1' => $agent->phone])->count();
            $agent->yandex_count = Synchronization::find()->where(['id_sources' => 2])->andWhere(['>', 'date_start', time() - 2 * 30 * 24 * 60 * 60])->andWhere(['phone1' => $agent->phone])->count();
            $agent->avito_count = Synchronization::find()->where(['id_sources' => 3])->andWhere(['>', 'date_start', time() - 2 * 24 * 30 * 60 * 60])->andWhere(['phone1' => $agent->phone])->count();
            $agent->youla_count = Synchronization::find()->where(['id_sources' => 4])->andWhere(['>', 'date_start', time() - 2 * 30 * 24 * 60 * 60])->andWhere(['phone1' => $agent->phone])->count();
            $agent->cian_count = Synchronization::find()->where(['id_sources' => 5])->andWhere(['>', 'date_start', time() - 2 * 30 * 24 * 60 * 60])->andWhere(['phone1' => $agent->phone])->count();
            $agent->status = 1;


            // если где то в истории в одном и томже источнике два объявления то скорее всего агент! ($agent->person_type = 1)
            $count = 1;
            if (($agent->irr_count > $count) or ($agent->yandex_count > $count) or ($agent->avito_count > $count) or ($agent->youla_count > $count) or ($agent->cian_count > $count)) {
                $agent->person_type = Agents::PERSON_TYPE_AGENT;
                $agent->count_ads = $agent->irr_count + $agent->yandex_count + $agent->avito_count + $agent->youla_count + $agent->cian_count;
                $agent->irr_count = Synchronization::find()->where(['id_sources' => 1])->andWhere(['phone1' => $agent->phone])->count();
                $agent->yandex_count = Synchronization::find()->where(['id_sources' => 2])->andWhere(['phone1' => $agent->phone])->count();
                $agent->avito_count = Synchronization::find()->where(['id_sources' => 3])->andWhere(['phone1' => $agent->phone])->count();
                $agent->youla_count = Synchronization::find()->where(['id_sources' => 4])->andWhere(['phone1' => $agent->phone])->count();
                $agent->cian_count = Synchronization::find()->where(['id_sources' => 5])->andWhere(['phone1' => $agent->phone])->count();

                info("PERSON_TYPE_AGENT", 'alert');
                //  $this->status_blacklist2 = 1;
            } else {
                info("PERSON_TYPE_HOUSEKEEPER", 'success');
                $agent->person_type = Agents::PERSON_TYPE_HOUSEKEEPER;
            }
            if (!$agent->save()) my_var_dump($agent->getErrors());

        }


    }

    public
    function updateToSale($attributes = [])
    {
        if ($attributes) {
            $sale = Sale::findOne($this->id);
            foreach ($attributes as $attribute) {
                $sale[$attribute] = $this[$attribute];
            }
            $sale->save();
        }

    }

    public
    function setAsAgent()
    {
        $sync = Synchronization::findOne($this->id);
        $sync->status_blacklist2 = 1;
    }


    /*
     * метод для установки статуса  и времени последней проверки
     *
     */
    public
    function setDisactive($response)
    {
        if ($response['date_start'] != $this->date_start) $this->disactive = 7; else $this->disactive = 6;
        $this->date_of_check = time();

    }

    /*
     * метод для автозагрузки tags
     *
     */

    public
    function AutoLoadTags($highlighting = false)
    {
        $tags = [];
        //    $Patterned_Tags = Tags::find()->where(['<>', 'patterns', ''])->all();

        if (empty(Yii::$app->cache->get('patterned_tags'))) {
            Yii::$app->cache->set('patterned_tags', Tags::find()->where(['<>', 'patterns', ''])->all());

        }
        $Patterned_Tags = Yii::$app->cache->get('patterned_tags');
        //  echo "<hr> есть " . count($Patterned_Tags) . " для автозагрузки Tags";
        //  echo "<br><h4>".$this->id."</h4> deszcription = ".$this->description;

        foreach ($Patterned_Tags as $tag) {
            $array_of_patterns = explode('&', $tag->patterns);
            foreach ($array_of_patterns as $pattern) {
                if (preg_match("/" . $pattern . "/iu", $this->description, $output)) {
                    //  echo "<br> нашли<span class=\"badge badge-success\">".$output[0]."</span> AS <span class=\"badge badge-indigo\">".$pattern."</span>";
                    array_push($tags, $tag->id);
                    break;
                }// else echo "<span class=\"badge badge-warning\">".$pattern."</span>";

            }
            if (!empty($tag->minus_patterns)) {
                $array_of_patterns = explode('&', $tag->minus_patterns);
                foreach ($array_of_patterns as $pattern) {
                    if (preg_match("/" . $pattern . "/iu", $this->description, $output)) {
                        //  echo "<br> удалили т.к. нашли<span class=\"badge badge-danger\">".$output[0]." AS ".$pattern."</span>";
                        $tags = MyArrayHelpers::DeleteFromArray($tag->id, $tags);
                        break;
                    }

                }
            }
        }
        if (!empty($tags)) {
            $this->setAutoloadedTags($tags);
        } else $this->tags_id = '';
//        $this->tags_autoload = 1;
//        echo "<br>";
//        echo "<br>";
//        $tags = Tags::find()->select(['name', 'color', 'type'])->where(['in', 'id', $tags])->orderBy('type,color')->asArray()->all();
//        $grouped_tags = array_group_by($tags, 'type');
//        //  my_var_dump($tags);
//        foreach ($grouped_tags as $key => $tags) {
//            echo "<br>" . Tags::PUBLIC_TYPES_ARRAY[$key] . ": ";
//            foreach ($tags as $tag) {
//                echo "<span class=\"badge badge-" . $tag['color'] . "\">#" . $tag['name'] . "</span> ";
//            }
//        }
    }

    public
    function setAutoloadedTags($tags)
    {

//        foreach ($tags as $tag) {
////            $real_tags = RealTags::find()->where(['sale_id' => $this->id])->andwhere(['tag_id' => $tag])->one();
////            if (!$real_tags) {
////                $real_tags = new RealTags();
////                $real_tags->user_id = 0;
////                $real_tags->sale_id = $this->id;
////                $real_tags->tag_id = $tag;
////                if (!$real_tags->save()) my_var_dump($real_tags->getErrors());
////            }
//
//        }

        info(Tags::convertToString($tags));
        $this->tags_id = Tags::convertToString($tags);
    }


}
