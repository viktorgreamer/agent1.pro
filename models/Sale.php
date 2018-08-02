<?php

namespace app\models;

use app\components\TagsWidgets;
use app\models\SalefiltersModels\SaleFiltersOnBlack;
use app\models\SalefiltersModels\SaleFiltersOnControl;
use phpDocumentor\Reflection\DocBlock\Tag;
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\models\Renders;


/**
 * This is the model class for table "Velikiy_Novgorod_sale".
 *
 * @property integer $id
 * @property integer $original_date
 * @property integer $count_of_views
 * @property integer $date_start
 * @property integer $rooms_count
 * @property string $title
 * @property integer $price
 * @property integer $id_similar
 * @property string $phone1
 * @property string $city
 * @property string $address
 * @property integer $house_type
 * @property double $coords_x
 * @property double $coords_y
 * @property integer $id_address
 * @property integer $year
 * @property string $locality
 * @property string $description
 * @property integer $floor
 * @property integer $floorcount
 * @property integer $id_sources
 * @property integer $grossarea
 * @property integer $kitchen_area
 * @property integer $living_area
 * @property string $images
 * @property string $url
 * @property integer $status_unique_phone
 * @property integer $load_analized
 * @property integer $tags_autoload
 * @property integer $status_unique_date
 * @property integer $status_blacklist2
 * @property string $person
 * @property string $id_irr_duplicate
 * @property integer $geocodated
 * @property integer $sync
 * @property integer $moderated
 * @property integer $processed
 * @property integer $broken
 * @property integer $average_price
 * @property integer $average_price_count
 * @property integer $average_price_address
 * @property integer $average_price_address_count
 * @property integer $average_price_same
 * @property integer $average_price_same_count
 * @property integer $radius
 * @property string $id_in_source;
 * @property integer $date_of_check
 * @property integer $disactive
 */
class Sale extends \yii\db\ActiveRecord
{

    const NEW_ITEM = 3;
    const PRICE_CHANGED = 4;
    const ADDRESS_CHANGED = 5;
    const THE_SAME = 6;
    const DATESTART_UPDATE = 7;
    const REINCARNED = 8;
    const CHECK_FOR_AGENTS = 9;
    const PARSED = 10;
    const GEOCODATED = 11;
    const ANALYSED = 12;
    const SALEFILTER_CHECKED = 13;
    const SIMILARED = 14;
    const SYNC_UP = 15;
    const SYNC_DOWN = 16;
    const MODERATED = 17;
    const PROCESSED = 18;

    // STOP READY DONE STATUSES
    const STOP = 1;
    const READY = 2;
    const DONE = 3;

    //
    const BROKEN_AVITO_PHONES = 5;



    public static function mapProcessingLogs()
    {
        return [
            self::NEW_ITEM => "NEW_ITEM",
            self::PRICE_CHANGED => "PRICE_CHANGED",
            self::ADDRESS_CHANGED => "ADDRESS_CHANGED",
            self::THE_SAME => "THE_SAME",
            self::DATESTART_UPDATE => "DATESTART_UPDATE",
            self::REINCARNED => "REINCARNED",
            self::CHECK_FOR_AGENTS => "CHECK_FOR_AGENTS",
            self::PARSED => "PARSED",
            self::GEOCODATED => "GEOCODATED",
            self::ANALYSED => "ANALYSED",
            self::SALEFILTER_CHECKED => "SALEFILTER_CHECKED",
            self::SIMILARED => "SIMILARED",
            self::SYNC_UP => "SYNC_UP",
            self::SYNC_DOWN => "SYNC_DOWN",
            self::MODERATED => "MODERATED",
            self::PROCESSED => "PROCESSED",
        ];
    }

    const DISACTIVE_CONDITIONS_ARRAY = [
        0 => 'ACTIVE',
        1 => 'DELETED',
        2 => 'DISABLED',
        3 => 'MAN_SOLD',
        4 => 'ACTIVE - SOLD',
        5 => "BROKEN_AVITO_PHONES",
        6 => "IS NULL"
//        3 => 'NEW',
//        4 => 'PRICE_CHANGED',
//        5 => 'ADDRESS_CHANGED',
//        6 => 'THE_SAME',
//        7 => 'DATESTART_UPDATE',
//        8 => 'DISABLED BUT ACTIVE',
//        9 => 'UPDATED',

    ];
    const ACTIVE = 0;
    const STATUSES_ARRAY = [
//        0 => 'ACTIVE',
//        1 => 'DELETED',
//        2 => 'DISABLED',
        3 => 'NEW',
        4 => 'PRICE_CHANGED',
        5 => 'ADDRESS_CHANGED',
        6 => 'THE_SAME',
        7 => 'DATESTART_UPDATE',
        8 => 'DISABLED BUT ACTIVE',
        9 => 'REINCARNED',

    ];
    const MAN_SOLD = 3;
    const STATUS_NAMES = [
        'parsed' => Sale::TYPE_OF_PARSED,
        'geocodated' => Geocodetion::GEOCODATED_STATUS_ARRAY,
        'processed' => Sale::TYPE_OF_PROCCESSED,
        'load_analized' => Sale::TYPE_OF_ANALIZED,
        'sync' => Sale::TYPE_OF_SYNC,
        'tags_autoload' => Sale::TYPE_OF_TAGS_AUTOLOAD,
        'moderated' => Sale::TYPE_OF_MODERATED,
        'disactive' => Sale::DISACTIVE_CONDITIONS_ARRAY,

    ];
    const ROOMS_COUNT_ARRAY = [
        1 => '1к',
        2 => '2к',
        3 => '3к',
        4 => '4к',
        5 => '5+',
        10 => 'Св.Планир.',
        20 => 'Студия',
        30 => 'Комната'

    ];

    const TYPE_OF_SYNC = [
        1 => 'NOT',
        2 => 'READY',
        3 => 'DONE'
    ];
    const TYPE_OF_PARSED = [
        1 => 'NOT',
        2 => 'READY',
        3 => 'DONE'
    ];
    const ID_SOURCES = [
        1 => 'irr.ru',
        2 => 'yandex.ru',
        3 => 'avito.ru',
        4 => 'youla.io',
        5 => 'cian.ru'

    ];
    const IMG_SOURCES = [
        1 => 'irr.png',
        2 => 'yandex.png',
        3 => 'avito.jpg',
        4 => 'youla.png',
        5 => 'cian.jpg'

    ];

    const HOUSE_TYPES = [
        0 => 'Любой',
        1 => 'пан.',
        2 => 'кирп.',
        3 => 'монолит.',
        4 => 'блочн.',
        5 => 'дерев.'

    ];


    const TYPE_OF_PROCCESSED = [
        '1' => 'NOT',
        '2' => 'READY',
        '3' => 'DONE'
    ];

    const TYPE_OF_ANALIZED = [
        '1' => 'NOT',
        '2' => 'READY',
        '3' => 'DONE',
        '4' => 'DENIED'
    ];
    const TYPE_OF_TAGS_AUTOLOAD = [
        '1' => 'NOT',
        '2' => 'READY',
        '3' => 'DONE'
    ];
    const TYPE_OF_MODERATED = [
        '1' => 'NOT',
        '2' => 'READY',
        '3' => 'DONE',
        '4' => 'ONCALL'
    ];

    public static $tablePrefix;

    public static function getFloors()
    {
        return [0 => 'Любой'] + range(0, 20);
    }

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale";
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
            [['id'], 'required'],
            [['id', 'date_start', 'rooms_count', 'price', 'id_address', 'house_type', 'floor', 'floorcount', 'id_sources', 'year',
                'load_analized', 'parsed', 'geocodated', 'sync', 'moderated', 'disactive', 'status_unique_date', 'status_blacklist2', 'average_price', 'average_price_count', 'average_price_address', 'average_price_address_count', 'average_price_same', 'average_price_same_count'], 'integer'],
            [['coords_x', 'coords_y', 'grossarea', 'living_area', 'kitchen_area'], 'number'],
            [['description', 'images', 'url', 'person', 'id_irr_duplicate', 'tags_id'], 'string'],
            [['title', 'phone1', 'phone2', 'city', 'address', 'locality'], 'string', 'max' => 255],
            [['id_in_source'], 'string', 'max' => 40],
            [['tags_id'], 'string', 'max' => 1000],
            [['id'], 'unique'],
        ];
    }

    public function TimeBetween($time, $seconds)
    {
        if (($this->date_start > ($time - $seconds)) && ($this->date_start < ($time + $seconds))) {
            return true;
        } else {
            return false;
        }
    }

    public function PriceBetween($price, $divergense)
    {
        if (($this->price > ($price - $divergense)) && ($this->price < ($price + $divergense))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date_start' => 'Date Start',
            'rooms_count' => 'Rooms Count',
            'title' => 'Title',
            'price' => 'Price',
            'phone1' => 'Phone1',
            'city' => 'City',
            'address' => 'Address',
            'coords_x' => 'Coords X',
            'coords_y' => 'Coords Y',
            'id_street' => 'Id Street',
            'description' => 'Description',
            'floor' => 'Floor',
            'floorcount' => 'Floorcount',
            'id_sources' => 'Id Sources',
            'grossarea' => 'Grossarea',
            'images' => 'Images',
            'url' => 'Url',
            'load_analized' => 'Status Unique Advert',
            'status_unique_date' => 'Status Unique Date',
            'status_blacklist2' => 'Status Blacklist2',
            'sync' => 'статус синхронизации',
            'agents' => 'Агенты',
            'housekeepers' => 'Хозяева',
        ];
    }
//
//    public function getTags()
//    {
//        return Tags::convertToArray($this->tags_id);
//
//    }

    public function getTags()
    {
        if ($this->similar) {
            $tags_sale = $this->similar->tags;
        } else $tags_sale = Tags::convertToArray($this->tags_id);
        // $tags_sale = $sale->tags;
        if (($this->addresses)) {
            $tags = array_merge($this->addresses->getTags(), $tags_sale);
        } else $tags = $tags_sale;

        return $tags;

    }

    public function getTagsNew()
    {
        $tags_similar = $this->similar->tags_id;
        if (!$this->similar) $tags_sale = $this->tags_id;
        $tags_address = $this->addresses->tags_id;
        $tags_all = preg_replace("/,,/", ",", $tags_similar . $tags_address . $tags_sale);

        $tags = Tags::convertToArray($tags_all);

        return $tags;

    }

    public function getTagsAddress()
    {

        if ($this->addresses) {
            return $this->addresses->getTags();
        } else return [];
    }

    public function getTagsSale()
    {

        if ($this->similarNew) {
            return $this->similarNew->tags;
        } else  return Tags::convertToArray($this->tags_id);
    }


    public function setTags($insert)
    {
        $this->tags_id = Tags::convertToString($insert);
    }

    public function getControls()
    {
        return $this->hasOne(SaleFiltersOnControl::className(), ['id_similar' => 'id_similar'])
            ->onCondition(['controls.id_salefilter' => Yii::$app->params['id_salefilter']]);
    }

    public function beforeSave($insert)
    {
        // при изменении статуса на 9 надо обнулить id_address
        if ($this->geocodated == 9) $this->id_address = 0;
        return parent::beforeSave($insert);
    }

    public function getAddresstags()
    {
        return $this->hasMany(RealTags::className(), ['id_address_tag' => 'id_address']);
        //->select('tag_id');
        // ->select('tag_id');

    }

    public function setProccessingLog($type, $was = null, $now = null)
    {
        if (!LOG_OF_ALGHOTITM) return false;
        $ProcessingLog = new ProcessingLog();
        $ProcessingLog->sale_id = $this->id;
        $ProcessingLog->type = $type;
        $ProcessingLog->time = time();
        $ProcessingLog->was = $was;
        $ProcessingLog->now = $now;
        if (!$ProcessingLog->save()) my_var_dump($ProcessingLog->errors);
    }

    public function getAddress()
    {
        return $this->hasOne(Addresses::className(), ['id' => 'id_address']);
        // ->viaTable(Tags::tableName(),['id' => 'tag_id']);

    }

    public function getLog()
    {
        return $this->hasMany(SaleLog::className(), ['sale_id' => 'id'])->all();

    }
    public function getPlogs()
    {
        return $this->hasMany(ProcessingLog::className(), ['sale_id' => 'id']);

    }

    public function getLogs()
    {
        return $this->hasMany(SaleLog::className(), ['sale_id' => 'id']);
    }


    public function RenderLog()

    {
        $logs = $this->getLogs()->orderBy('date')->all();
        $body = '';
        if ($logs) {
            foreach ($logs as $log) {
                $body .= "<br>" . span(Sale::STATUSES_ARRAY[$log->type] . " " . date("d.m.y H:i:s", (int)$log->date) . " '" . $log->was . "'->'" . $log->now);
            }
        }
        return $body;
    }
    public function RenderProcessingLog()

    {
        $logs = $this->getPlogs()->orderBy('time')->all();
        $body = '';
        if ($logs) {
            foreach ($logs as $log) {
                $body .= "<br>" . span(Sale::mapProcessingLogs()[$log->type] . " " . date("d.m.y H:i:s", (int)$log->time));
                // . " '" . $log->was . "'->'" . $log->now);
            }
        }
        return $body;
    }

    public static function RenderOneLog($log)

    {
        $body = span(Sale::STATUSES_ARRAY[$log[0]] . " " . date("d.m.y H:i:s", (int)$log[1]) . " '" . $log[2] . "'->'" . $log[3]);
        return $body;
    }

    public function getSimilarSales1() {
        return $this->hasMany(Sale::className(),['id_similar' => 'id'])->viaTable(SaleSimilar::tableName(),['id' => 'id_similar']);
    }

    /*
    * основной сортировочный метод при последовательной обработке  модели
    */
    public function changingStatuses($name)
    {
        switch ($name) {
            // если пришла новая модель, то готовим ее к парсингу
            case "NEW" :
                $this->parsed = 2;

                // info("меняем статус на NEW", 'info');
                return 'NEW';
            // если спарсили модель, то можно отправлять на геокодирование
            case "PARSED" :
                // info("провели парсинг", 'info');
                $this->parsed = 3;
                $this->geocodated = 8;
                $this->setProccessingLog(self::PARSED);
                return 'PARSED';
            // если геокодирование прошло, то готовимся в анализу и обработке если статус не равен 9, если нет то ставим стутус невозможности анализа
            case "GEOCODATED" :
                {
                    // info("проводим геокодирование", 'info');
                    if ($this->geocodated != 9) $this->load_analized = 2; else $this->load_analized = 4;
                    $this->id_similar = 0;
                    $this->processed = 2;
                    $this->setProccessingLog(self::GEOCODATED);

                    return 'GEOCODATED';
                }
            case "PROCESSED" :
                {
                    // info("PROCESSED", 'info');
                    $this->processed = 3;
                    // если прошли все пункты то оправляем на синхронизацию и модерацию
                    if (in_array($this->load_analized, [3, 4])) {
                        $this->sync = 2;
                    }
                    // в случае если PRICE_CHANGED морерацию не проводим
                    if ($this->status != 4) $this->moderated = 2;
                    $this->setProccessingLog(self::PROCESSED);

                    return "PROCESSED";
                }
            case "MANUAL_GEOCODATION" :
                {
                    // info("изменили адрес", 'info');
                    $this->geocodated = 7;
                    $this->id_similar = 0;
                    $this->processed = 2;
                    $this->load_analized = 2;
                    $this->sync = 2;
                    $this->setProccessingLog(self::GEOCODATED);

                    return 'MANUAL_GEOCODATION';
                }
            case "LOAD_ANALIZED" :
                {
                    // info("проводим анализ", 'info');
                    $this->load_analized = 3;
                    $this->sync = 2;
                    $this->setProccessingLog(self::ANALYSED);

                    return "LOAD_ANALIZED";
                }
            case "SYNC" :
                {
                    // info("синхронизируемся", 'info');
                    $this->sync = 3;

                    break;
                }
            case "MODERATED" :
                {
                    //  info("провели модерацию", 'info');
                    $this->moderated = 3;
                    // для download синхронизации
                    $this->sync = 2;
                    $this->setProccessingLog(self::MODERATED);

                    return "MODERATED";
                }
            case "ADDRESS_CHANGED" :
                {
                    // info("ADDRESS_CHANGED", 'primery');
                    $this->status = self::ADDRESS_CHANGED;
                    $this->parsed = self::READY;
                    $this->load_analized = self::STOP;
                    $this->geocodated = Geocodetion::STOP;
                    $this->processed = self::STOP;
                    $this->sync = self::STOP;
                    $this->moderated = self::STOP;
                    if (in_array($this->disactive, [1, 2])) {
                        $log = [9, time(), '', ''];
                        $this->addLog($log);
                        $this->disactive = 0;
                        $this->setProccessingLog(self::REINCARNED);


                    }
                    $this->setProccessingLog(self::ADDRESS_CHANGED);


                    /*$similar = SaleSimilar::findOne($this->id_similar);
                    if ($similar) {
                       if (count(Methods::convertToArrayWithBorders($similar->similar_ids_all)) == 0) {
                            info(" DETELING SIMILAR, BECAUSE THERE ARE NO ITEMS IN IT");
                            $similar->delete();
                        }
                    }*/

                    $this->id_similar = 0;
                    return "ADDRESS_CHANGED";
                }
            case "PRICE_CHANGED" :
                {
                    //  info("PRICE_CHANGED", 'primery');
                    if ($this->status != self::ADDRESS_CHANGED) {
                        if (in_array($this->disactive, [1, 2])) {
                            $log = [9, time(), '', ''];
                            $this->addLog($log);
                            $this->disactive = 0;
                            $this->setProccessingLog(self::REINCARNED);

                        }
                        $this->status = self::PRICE_CHANGED;
                        $this->processed = self::READY;
                    }
                    $this->sync = self::STOP;
                    $this->setProccessingLog(self::PRICE_CHANGED);

                    return "PRICE_CHANGED";
                }
            case "THE_SAME" :
                {
                    // info("THE_SAME", 'primery');
                    if (($this->disactive == 1) or ($this->disactive == 2)) {
                        $log = [9, time(), '', ''];
                        $this->addLog($log);
                        $this->disactive = 0;
                        info("REINCARNATION OF ITEM");
                        $this->status = 9;
                        $this->sync = 2;
                        $this->setProccessingLog(self::REINCARNED);
                        $this->save();
                        return " REINCARNED";


                    } else return "THE_SAME";
                }
            case "DATESTART_UPDATED" :
                {
                    // info("DATESTART_UPDATED", 'primary');
                    if (($this->status != 5) and ($this->status != 4)) {
                        $this->status = 7;
                        $this->sync = 2;
                    }
                    if (in_array($this->disactive, [1, 2])) {
                        $log = [9, time(), '', ''];
                        $this->addLog($log);
                        $this->disactive = 0;
                        $this->setProccessingLog(self::REINCARNED);

                    }
                    $this->setProccessingLog(self::DATESTART_UPDATE);

                    return "DATESTART_UPDATED";
                }
            case "DELETED" :
                {
                    //info("DELETED", 'primary');
                    $this->disactive = 1;
                    $this->sync = 2;
                    return "DELETED";
                }
            case "DISABLED" :
                {
                    //  info("DISABLED", 'primary');
                    $this->disactive = 2;
                    $this->sync = 2;
                    return "DISABLED";
                }
            default :
                return "<h1> ВЫ ВВЕЛИ НЕСУЩЕСТВУЮЩЕЕ ПРАВИЛО ИЗМЕНЕНИЯ СТАТУСОВ</h1>";
        }

    }


    /**
     * @inheritdoc
     * @return saleQuery the active query used by sale AR class.
     */
//    public static function find()
//    {
//        return new SaleQuery(get_called_class());
//    }


// метод парсинга всех данных
    public function ParsingSale($adv, $is_room = false)
    {

        // парсим время
        $this->date_start = parsing_date($adv->time);

        // если пришел заголовок то
        if (isset($adv->title) && $adv->title != '') {

            // парсим этаж этажность
            if (empty($adv->param_2415)) {
                $this->floorcount = parsing_floorcounts($adv->title);
            } else {
                $this->floorcount = $adv->param_2415;
            }
            // парсим этаж
            if (empty($adv->param_2315)) {
                $this->floor = parsing_floors($adv->title);
            } else {
                $this->floor = $adv->param_2315;
            }

            // Получаем площадь
            $this->grossarea = parsing_grossarea($adv->title);
            if ($is_room) $this->rooms_count = 30;
            else {
                if (($adv->param_1945 != 'Студия')) {
                    $this->rooms_count = $adv->param_1945;
                } else $this->rooms_count = 20;


                // $this->rooms_count = rooms_count($adv->title);
            }

        };
        // парсим id ресурса
        $this->id_sources = parsing_id_resourse($adv->source);
        // парсинг телефон
        $this->phone1 = parsing_phone($adv->phone);
        // парсим тип дома
        $this->house_type = parsing_house_type($adv->param_2009);


        // парсинг улицы
        $this->address = $adv->address;

        // вставляем фотки
        $images = '';
        foreach ($adv->images as $key => $value) {
            $images = $images . "X" . $value->imgurl;
        }

        // источник url
        $this->id = $adv->id;
        $this->url = $adv->url;
        $this->price = $adv->price;

        // определяем id street
        //$id_street = get_street_id($afv->address, $id_city);

        $this->description = $adv->description;
        $this->address = $this->address;
        $this->title = $adv->title;
        $this->images = $images;
        $this->id_address = 0;
        $this->url = $this->url;
        $this->city = $adv->city;
        $this->person = $adv->person;
        $coords_array = $adv->coords;

        $this->coords_x = round($coords_array->lat, 5);
        $this->coords_y = round($coords_array->lng, 5);
        /* echo "<tr>";
         echo "<td>{$adv->id}</td>";
         echo "<td><a href=" . $adv->url . ">{$adv->title}</a></td>";
         echo "<td>{$adv->param_2009}</td>";
         echo "</tr>";

         echo "<tr>";
         echo "<td>{$this->id}</td>";
         echo "<td>{$this->title}</td>";
         echo "<td>{$this->house_type}</td>";
         echo "</tr>";*/


        // вставляем данные в таблицу
        if ($adv->nedvigimost_type == 'Продам') {

            // удаление полного дубликата irr
            if ($this->id_sources == '1') { // если ресурк ирр то проверяем на наличие дубликатов


                /*  $sale_irr_dublicаte = SaleHistory::find()
                      ->andWhere(['rooms_count' => $this->rooms_count])
                      ->andWhere(['phone1' => $this->phone1])
                      ->andWhere(['address' => $this->address])
                      ->andWhere(['id_sources' => $this->id_sources])
                      ->one();
                  if ($sale_irr_dublicаte) {
                      // $sale_irr_dublicаte->delete();
                      return "update";

                      echo "удален дубликат irr";
                  }*/
            }

            if ($this->save()) {
                return "save";
            } else {
                $saved_sale = Sale::find()->where(['id' => $adv->id])->one();
                if ($saved_sale) {
                    $saved_sale->rooms_count = $this->rooms_count;
                    $saved_sale->price = $this->price;
                    $saved_sale->description = $this->description;
                    if ($saved_sale->save()) return "update";
                    else {
                        return "error";
                        print_r($saved_sale->getErrors());
                    }
                }

            }
        }


    }

    public
    function UpdateAddress($response, $log)
    {
        $this->address_line = $response['address_line'];

        $this->url = $response['url'];
        $this->addLog($log);


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


    public function is_in_phoneblacklist()
    {

        // проверка есть ли данный телефон в списке агентов то обновляем count_ads++;
        $is_in_phoneblacklist = Agents::findOne(['phone' => $this->phone1, 'type' => '1']);

        if ($is_in_phoneblacklist) { //  если такой телефон есть в базе PhoneBlacklist

            $this->status_blacklist2 = 2; // то обновляем статус status_blacklist2 = 2 - объявление агента
            $this->status_unique_date = 1; // обновляем что данная строка обработана прошла проверку на phone_blacklist `status_unique_date` = '1'
            $is_in_phoneblacklist->count_ads++; //  обновляем количество объявлений данного агента
            // попытка занести разнгые имена агентов на один телефон
            /* if (!in_array($item->person, explode(",", $is_in_phoneblacklist->person)) and $item->person != '') {
                 echo "было".$is_in_phoneblacklist->person." стало ".$is_in_phoneblacklist->person + "," + $item->person;
                 $is_in_phoneblacklist->person = $is_in_phoneblacklist->person + "," + $item->person;

             }*/
            $is_in_phoneblacklist->save();
            if ($this->save()) {
//                     print_r($update_sale->getErrors());
                echo " Объявление агента " . $this->id;
            }
            return true;
        }

    }

    public function is_in_proccessed_sale()
    {
        //  проверка на то что из обработанных раннее объявлений есть такие же телефоны
        $is_in_proccessed_sale = Sale::find()->where(['phone1' => $this->phone1])
            ->andWhere(['status_unique_date' => 1])
            ->one();
        if ($is_in_proccessed_sale) {

            $is_in_proccessed_sale->status_blacklist2 = 2; // то обновляем статус status_blacklist2 = 2 - объявление агента
            $is_in_proccessed_sale->status_unique_date = 1; // обновляем что данная строка обработана прошла проверку на phone_blacklist `status_unique_date` = '1'
            if ($is_in_proccessed_sale->save()) {
//                            print_r($update_sale->getErrors());
                echo " Объявление агента " . $is_in_proccessed_sale->id;
            }

            $this->status_blacklist2 = 2; // то обновляем статус status_blacklist2 = 2 - объявление агента
            $this->status_unique_date = 1; // обновляем что данная строка обработана прошла проверку на phone_blacklist `status_unique_date` = '1'
            if ($this->save()) {
//                     print_r($update_sale->getErrors());
                echo " Объявление агента " . $this->id;
            }

            // добавляем новый телефон в список агентов
            $new_phone_blacklist = new Agents();
            $new_phone_blacklist->phone = $this->phone1;
            $new_phone_blacklist->type = 1;
            $new_phone_blacklist->count_ads = 2;
            if (!empty($this->person)) $new_phone_blacklist->person = $this->person;
            if ($new_phone_blacklist->save()) {
//                            print_r($new_phone_blacklist->getErrors());
                echo " добавлен новый телефон агента " . $this->phone1;
            };
            return true;

        }
    }

    public function update_as_homekeeper_sale()
    {
        $this->status_blacklist2 = 1; // то обновляем статус status_blacklist2 = 1 - объявление не агента
        $this->status_unique_date = 1; // обновляем что данная строка обработана прошла проверку на phone_blacklist `status_unique_date` = '1'

    }

    // если не прошли первая и вторая проверки тогда по всей вероятности это объявление не агента

    public function average_price($period = 12)
    {


        return SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['floorcount' => $this->floorcount])
            ->andFilterWhere(['grossarea' => $this->grossarea])
            ->average('price');
    }

    public function average_price_count($period = 12)
    {

        return SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andFilterWhere(['floorcount' => $this->floorcount])
            ->andFilterWhere(['grossarea' => $this->grossarea])
            ->count();

    }

    public function average_price_address($period = 12)
    {

        // вычислем среднюю цену вариантов по данному id_address
        return SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
            ->andWhere(['id_address' => $this->id_address])
            ->average('price');

    }

    public function average_price_address_count($period = 12)
    {

        // вычислем среднюю цену вариантов по данному id_address
        return SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['>', 'date_start', (time() - $period * 30 * 24 * 60 * 60)])
            ->andfilterwhere(['<', 'grossarea', ($this->grossarea * 1.1)])
            ->andfilterwhere(['>', 'grossarea', ($this->grossarea * 0.9)])
            ->andWhere(['id_address' => $this->id_address])
            ->count();

    }

    public function average_price_statistic()
    {

        $item = SaleAnalitics::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['floorcount' => $this->floorcount])
            ->andwhere(['year' => $this->year()])
            ->andFilterWhere(['house_type' => $this->house_type])
            ->andFilterWhere(['grossarea' => $this->grossarea])
            ->one();

        return $item->average_price;
    }

    public function average_price_count_statistic()
    {

        $item = SaleAnalitics::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andFilterWhere(['floorcount' => $this->floorcount])
            ->andFilterWhere(['house_type' => $this->house_type])
            ->andwhere(['year' => $this->year()])
            ->andFilterWhere(['grossarea' => $this->grossarea])
            ->one();

        return $item->average_price_count;

    }

    public function average_price_address_statistic()
    {

        // вычислем среднюю цену вариантов по данному id_address
        $item = SaleAnaliticsAddress::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->filterWhere(['grossarea' => $this->grossarea])
            ->andWhere(['id_address' => $this->id_address])
            ->one();
        return $item->average_price;

    }

    public function average_price_address_count_statistic($period = 12)
    {

        // вычислем среднюю цену вариантов по данному id_address
        $item = SaleAnaliticsAddress::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->filterWhere(['grossarea' => $this->grossarea])
            ->andWhere(['id_address' => $this->id_address])
            ->one();
        return $item->average_price_count;

    }

    public function load_statistic()
    {

        // вычислем среднюю цену и количество вариантов по данному id_address
//        $SaleAnaliticsAddress = SaleAnaliticsAddress::find()
//            ->filterWhere(['rooms_count' => $this->rooms_count])
//            ->filterWhere(['grossarea' => $this->grossarea])
//            ->andWhere(['id_address' => $this->id_address])
//            ->one();


        // вычислем среднюю цену и количество вариантов по похожим вариантам данного id_address
        $SaleAnaliticsSameAddress = SaleAnaliticsSameAddress::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->filterWhere(['grossarea' => $this->grossarea])
            ->andWhere(['id_address' => $this->id_address])
            ->one();

        // вычислем среднюю цену и количество похожих по параметрам вариантов
        /* $SaleAnalitics = SaleAnalitics::find()
             ->filterWhere(['rooms_count' => $this->rooms_count])
             ->andFilterWhere(['floorcount' => $this->floorcount])
             ->andFilterWhere(['house_type' => $this->house_type])
             ->andfilterwhere(['year' => $this->year])
             ->andFilterWhere(['grossarea' => $this->grossarea])
             ->one();*/


        /*  if ($SaleAnalitics->average_price == 0) echo "- load_statistic_error -";
          $this->average_price = $SaleAnalitics->average_price;*/
        //   $this->average_price_count = $SaleAnalitics->average_price_count;
//        if ($SaleAnaliticsAddress->average_price == 0) echo "- load_statistic_address_error -";
//        $this->average_price_address = $SaleAnaliticsAddress->average_price;
//        $this->average_price_address_count = $SaleAnaliticsAddress->average_price_count;

        if ($SaleAnaliticsSameAddress->average_price == 0) echo "- load_statistic_same_address_error -";
        $this->average_price_same = $SaleAnaliticsSameAddress->average_price;
        $this->average_price_same_count = $SaleAnaliticsSameAddress->average_price_count;
        $this->radius = $SaleAnaliticsSameAddress->radius;
    }

    public function average_price_m2()
    {
        $summa_of_grossarea = SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andfilterWhere(['<>', 'grossarea', 0])
            ->sum('grossarea');

        $summa_of_price = SaleHistory::find()
            ->filterWhere(['rooms_count' => $this->rooms_count])
            ->andfilterWhere(['<>', 'grossarea', 0])
            ->sum('price');


        if ($summa_of_grossarea != 0) return round($summa_of_price / $summa_of_grossarea);
    }

    public function average_price_m2_all()
    {
        $summa_of_grossarea = SaleHistory::find()
            ->filterWhere(['<>', 'grossarea', 0])
            ->sum('grossarea');

        $summa_of_price = SaleHistory::find()
            ->filterWhere(['<>', 'grossarea', 0])
            ->sum('price');


        return round($summa_of_price / $summa_of_grossarea);
    }

    public function get_similar_sales($persent_of_price = 5, $distanse = 10000, $persent_of_grossarea = 5, $period_start = 10, $period_end = 0)
    {
        $similar_sales = Sale::find()
            ->where(['>=', 'price', $this->price * (100 - $persent_of_price) / 100])
            ->where(['<=', 'price', $this->price * (100 + $persent_of_price) / 100])
            ->andwhere(['>=', 'grossarea', $this->grossarea * (100 - $persent_of_price) / 100])
            ->andwhere(['<=', 'grossarea', $this->grossarea * (100 + $persent_of_price) / 100])
            ->andFilterWhere(['>=', 'date_start', (time() - $period_start * 86400)])
            ->andFilterWhere(['<=', 'date_start', (time() - $period_end * 86400)])
            ->all();

        return $similar_sales;


    }


    public
    function fill_the_missing_paramerts()
    {
        // ищем из истории где данногу id_присвоен параметр
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address->house_type != 0) {
            $this->house_type = $address->house_type;

        }
        if ($address->floorcount != 0) $this->floorcount = $address->floorcount;


    }

    public
    function get_average_price_address()
    {

    }

// проверка на то что такой записи больше нет
    public
    function is_unique_sale_analitics_address($prefix)
    {
        SaleAnaliticsAddress::setTablePrefix($prefix);
        $exist = SaleAnaliticsAddress::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['id_address' => $this->id_address])
            ->andwhere(['grossarea' => $this->grossarea])
            ->exists();
        echo " is unique stat address = " . $exist;
        return $exist;
    }

    public
    function is_unique_sale_analitics($prefix)
    {
        SaleAnalitics::setTablePrefix($prefix);
        $exist = SaleAnalitics::find()
            ->where(['rooms_count' => $this->rooms_count])
            ->andwhere(['grossarea' => $this->grossarea])
            ->andwhere(['house_type' => $this->house_type])
            ->exists();
        echo " is unique stat = " . $exist;
        return $exist;
    }



    // geolocation functions
    // main
    public function geolocate($prefix)
    {
        Addresses::setTablePrefix($prefix);
        // получаем координаты и точность ввиде массива
        $array_coords = $this->get_coords_and_precision();
        $precision = $array_coords[2];
        {
            // если координаты пришли, а также это новый адрес то пытаемся создать новый адрес
            if (($array_coords) and ($this->is_new_address($array_coords))) {
                echo '    Такого адреса еще нет в базе, заносим';
                $this->create_new_address($array_coords);
            }
            // ехпортируем из таблицы addresses id address а также подтягиваем параметры house type floorcount если они отсутсвтвуют
            $this->export_from_addresses($array_coords);

            // поиск данного объекта в базе sale
            /* $sale = Sale::findOne($this->id);
             if ($sale) {
                 $sale->locality = $this->locality;
                 $sale->status_unique_advert = 1;
                 $sale->id_address = $this->id_address;
                 $sale->save();
                 echo "Одновременно продекодировали sale";
             }
             if ($array_coords) {
                 echo "Геокодирование удалось: " . $this->id;
                 return true;
             }*/


        }
    }

    public
    function is_full()
    {
        if (($this->id_address != 0) and ($this->grossarea != 0)
            and ($this->rooms_count != 0) and ($this->house_type != 0) and ($this->floor != 0) and ($this->year != 0)
        ) return true;
    }

    public
    function is_full_salesimilar()
    {
        if (($this->id_address) and ($this->grossarea)
            and ($this->rooms_count) and ($this->floor)
        ) {
            info(' OBJECT READY FOR SALESIMILAR CHECK', 'success');
            return true;
        } else {
            info(' OBJECT NOT READY FOR SALESIMILAR CHECK', 'alert');
            return false;
        }
    }


    public
    function is_new_address($array_coords)
    {
        $is_new_address = !Addresses::find()
            ->where(['coords_x' => $array_coords[0]])
            ->andwhere(['coords_y' => $array_coords[1]])
            ->exists();

        return $is_new_address;


    }

    public
    function get_coords_and_precision()
    {

        $fulladdress = $this->city . "," . $this->address;
        // Обращение к http-геокодеру
        $xml_string = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($fulladdress) . '&results=1';
        // echo $xml_string;
        $xml = simplexml_load_file($xml_string);
        // Если геокодировать удалось, то записываем в БД
        $found = $xml->GeoObjectCollection->metaDataProperty->GeocoderResponseMetaData->found;
        echo "found = " . $found;
        if ($found != 0) {
            $precision = $xml->GeoObjectCollection->featureMember->GeoObject->metaDataProperty->GeocoderMetaData->precision->__ToString();
            $coords = str_replace(' ', ',', $xml->GeoObjectCollection->featureMember->GeoObject->Point->pos);

            $result = ' ' . $this->id . '-  полный адрес -' . $fulladdress . ' coords' . $coords . ' precision' . $precision . '<br>';
            echo $result;
            return explode(",", $coords . "," . $precision);
        } else return false;
    }

    public
    function back_xml_request($array_coords)
    {

        $coords = $array_coords[0] . "," . $array_coords[1];
        $xml_string_back = 'http://geocode-maps.yandex.ru/1.x/?geocode=' . urlencode($coords) . '&results=1';
        $xml_back = simplexml_load_file($xml_string_back);
        // т.к. адрес может быть введен коряво (т.е. неточно то ответ $precision геокодера может быть  exact или number)
        $found_name = $xml_back->GeoObjectCollection->featureMember->GeoObject->name;
        if ($found_name) $found_name = $xml_back->GeoObjectCollection->featureMember->GeoObject->name->__ToString();
        $found_locality_xml = $xml_back->GeoObjectCollection->featureMember->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->
        AdministrativeArea->SubAdministrativeArea->Locality->LocalityName;
        if ($found_locality_xml != '') $found_locality = $xml_back->GeoObjectCollection->featureMember->GeoObject->metaDataProperty->GeocoderMetaData->AddressDetails->Country->
        AdministrativeArea->SubAdministrativeArea->Locality->LocalityName->__ToString();
        else $found_locality = $xml_back->GeoObjectCollection->featureMember->GeoObject->name->__ToString();

        if ($found_name == '') $found_back = 'пусто';
        return [
            'found_name' => $found_name,
            'found_locality' => $found_locality
        ];

    }

    public
    function create_new_address($array_coords)
    {
        $precision = $array_coords[2];
        $new_address = new Addresses();
        $otvet = $this->back_xml_request($array_coords);
        var_dump($otvet);
        $new_address->house_type = $this->house_type;
        $new_address->coords_x = $array_coords[0];
        $new_address->coords_y = $array_coords[1];
        $new_address->floorcount = $this->floorcount;
        $new_address->locality = $otvet['found_locality'];
        if ($precision == 'exact') $new_address->address = $otvet['found_name']; // если адрес точный то записываем имя адреса из яндекса иначе тот что пришел
        if ($precision == 'number') {
            $new_address->address = $otvet['found_name'];
            $new_address->address_string_variants = $this->address;
        } // если адрес точный то записываем имя адреса из яндекса иначе тот что пришел
        if (($precision != 'exact') and ($precision != 'number')) $new_address->address = $this->address;
        $new_address->year = 0;
        $new_address->precision_yandex = $precision;
// еслиданные провалидировались то заносим их в базу
        if ($new_address->validate()) {
            if ($new_address->save()) {
            };
        } else {
            var_dump($new_address->errors);
        }
    }

    public
    function export_from_addresses($array_coords)
    {
        $precisions = ['exact', 'number', 'street', 'other'];

        // находим из таблицы адресов те которые соотвутствуют заданным координатам и точности
        $address = Addresses::find()
            ->where(['coords_x' => $array_coords[0]])
            ->andwhere(['coords_y' => $array_coords[1]])
            ->andWhere(['in', 'precision_yandex', $precisions])
            ->one();
        if ($address->id == 0) var_dump($address);
        echo " существующий id_address = " . $address->id;
        // если что то пришло то переносим правильный адрес и id_address
        if ($address) {
            $this->address = $address->address;
            $this->id_address = $address->id;
            $this->locality = $address->locality;
            // если пропущены какие то из параметров взаимный обмен параметрами про пропуске
            if (($address->house_type == 0) or ($address->floorcount == 0)) {
                // если попаким то причинам в таблице адресов пропущен параметр house type
                if (($address->house_type == 0) and ($this->house_type != 0)) {
                    echo "!!!!!!! !удалось прописать house type из других параметров";
                    $address->house_type = $this->house_type;
                }
                // если попаким то причинам в таблице адресов пропущен параметр floorcount
                if (($address->floorcount == 0) and ($this->floorcount != 0)) {
                    echo "!!!!!!!!!!!!!!удалось прописать floorcount из других параметров";
                    $address->floorcount = $this->floorcount;
                }
            }
        }
// если каките то параметры пришли то сохраняем address
        if ($address) {
            if ($address->save()) {
            } else {
                var_dump($address->errors);
            }
        }


        $this->status_unique_advert = 1;

        // переносим из модели аддрес если параметры hose_type или floorcount пришли нулевые
        if (($this->house_type == 0) and ($address->house_type != 0)) {
            $this->house_type = $address->house_type;
        }

        if (($this->floorcount == 0) and ($address->floorcount != 0)) {
            $this->floorcount = $address->floorcount;
        }

        if ($this->validate()) {
            $this->save();
            $message = " адрес  базе sale успешно обновлен";
        } else
            var_dump($this->errors);
    }


    public function parse_missing_house_type_parameters()
    {
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address) {
            if (($address->house_type != 0) and ($address->house_type != Null)) {

                // echo " house_type = " . $address->house_type;
                // находим все sale c данным id address
                $sales_to_change_house_type = Sale::find()
                    ->where(['id_address' => $this->id_address])
                    ->all();
                // пробегаемся по ним
                foreach ($sales_to_change_house_type as $item3) {


                    if ($sales_to_change_house_type) {
                        $sale_to_change_house_type = Sale::find()
                            ->where(['id' => $item3->id])
                            ->one();
                        $sale_to_change_house_type->house_type = $address->house_type;
                        // echo "удалось присвоить house_type id =" . $sale_to_change_house_type->id . "house_type = " . $address->house_type;
                        if (!$sale_to_change_house_type->save()) $sale_to_change_house_type->errors;
                    }
                }
            }
        }

    }

    public function parse_missing_floorcount_parameters()
    {
        $address = Addresses::find()
            ->where(['id' => $this->id_address])
            ->one();
        if ($address) {
            if (($address->floorcount != 0) and ($address->floorcount != Null)) {

                // echo " floorcount = " . $address->floorcount;
                // находим все sale c данным id address
                $sales_to_change_floorcount = Sale::find()
                    ->where(['id_address' => $this->id_address])
                    ->all();
                // пробегаемся по ним
                foreach ($sales_to_change_floorcount as $item3) {


                    if ($sales_to_change_floorcount) {
                        $sale_to_change_floorcount = Sale::find()
                            ->where(['id' => $item3->id])
                            ->one();
                        $sale_to_change_floorcount->floorcount = $address->floorcount;
                        //  echo "удалось присвоить floorcount id =" . $sale_to_change_floorcount->id . "floorcount = " . $address->floorcount;
                        if (!$sale_to_change_floorcount->save()) $sale_to_change_floorcount->errors;
                    }
                }
            }
        }

    }

    public function get_all_id_address_variants()
    {

        $sales = Sale::find()
            ->where(['id_address' => $this->id_address])
            ->andWhere(['rooms_count' => $this->rooms_count])
            ->orderBy('date_start DESC')
            ->all();
        return $sales;
    }

    public function import_year()
    {
        $address = Addresses::findOne($this->id_address);
        if ($address->year != 0) {
            $sale_history = Sale::findOne($this->id);
            $sale_history->year = $address->year;
            if (!$sale_history->save()) var_dump($sale_history->errors());
        }

    }

    public function set_original_date($date)
    {
        $sale = self::findOne($this->id);
        $sale->original_date = $date;
        $sale->save();

    }

    public function set_views($views)
    {
        $sale = self::findOne($this->id);
        $sale->count_of_views = $views;
        $sale->save();

    }

    public function count_per_day()
    {
        return round($this->count_of_views / (time() - $this->original_date) * 60 * 60 * 24);
    }

    public function coords_x()
    {
        $address = Addresses::findOne($this->id_address);
        return $address->coords_x;
    }

    public function coords_y()
    {
        $address = Addresses::findOne($this->id_address);
        return $address->coords_y;
    }

    public function getAll_tags()
    {
        if ($this->addresses) $tags = array_merge($this->addresses->getTags(), $this->tags);
        else $tags = $this->tags;;
        return $tags;
    }


    public function set_sold()
    {
        $this->disactive = 1;
        $this->save();
    }

    public function LoadStatistic()
    {
        // SaleAnalitics
        $SaleAnalitics = New SaleAnalitics();
        $SaleAnalitics->ExportParametersFromSale($this);
        if (!$SaleAnalitics->IsExists()) {
            $SaleAnalitics->CalculateStatistic();
            if (!$SaleAnalitics->save()) my_var_dump($SaleAnalitics->getErrors());
        }
        $SaleAnalitics->LoadToSale($this);

        // SaleAnaliticsAddress
        $SaleAnaliticsAddress = New SaleAnaliticsAddress();
        $SaleAnaliticsAddress->ExportParametersFromSale($this);
        if (!$SaleAnaliticsAddress->IsExists()) {
            $SaleAnaliticsAddress->CalculateStatisticAddress();

            if (!$SaleAnaliticsAddress->save()) my_var_dump($SaleAnaliticsAddress->getErrors());
        }
        $SaleAnaliticsAddress->LoadToSale($this);

        // SaleAnaliticsSameAddress
        $SaleAnaliticsSameAddress = New SaleAnaliticsSameAddress();
        $SaleAnaliticsSameAddress->ExportParametersFromSale($this);
        if (!$SaleAnaliticsSameAddress->IsExists()) {
            $SaleAnaliticsSameAddress->CalculateStatisticSameAddress();
            if (!$SaleAnaliticsSameAddress->save()) my_var_dump($SaleAnaliticsSameAddress->getErrors());

        }
        $SaleAnaliticsSameAddress->LoadToSale($this);


    }


    public function setSynchronized()
    {
        $Sync = Sale::findOne($this->id);
        $Sync->sync = 1;
        if (!$Sync->save()) {
            my_var_dump($Sync->getErrors());
            return false;
        } else return true;


    }

    public function getSimilar_ids()
    {
        $ids = Sale::find()->select('id')
            ->where(['floor' => $this->floor])
            ->andwhere(['id_address' => $this->address])
            ->andWhere(['in', 'disactive', [1, 2]])
            ->andWhere(['<>', 'id', $this->id])
            ->orderBy('price')
            ->asArray()
            ->all();
        if ($ids) {
            $this->similar_ids = implode(",", $ids);
            $sales = array_push($ids, $this->id);
            foreach ($ids as $id) {
                $sale = Self::findOne($id);
                $sale->similar_ids = implode(",", MyArrayHelpers::DeleteFromArray($id, $ids));
                $sale->sync = 0;
                $sale->save();
            }
        } else return false;
    }

    public function setModerated()
    {
        $similar = SaleSimilar::findOne($this->id_similar);
        if ($similar) $similar->moderated = 3;
        $similar->save();

        return $this->save();
    }


    /// методы и геттеры для образования данных для вывода
    /*
     * метод для рендеринга Url */
    public function renderUrl()
    {
        return "<a href=" . $this->url . " target=_blank>" . Sale::ID_SOURCES[$this->id_sources] . "</a>";
    }

    public function renderSource()
    {
        return "<a href=" . $this->url . " target=_blank>" . Html::img("@web/images/logo/" . Sale::IMG_SOURCES[$this->id_sources]) . "</a>";
    }

    /* метод для рендеринга rooms_count */
    public function renderRooms_count()
    {
        if ($this->rooms_count == 30) return "Комн."; elseif ($this->rooms_count == 20) return "Студия";
        else   return $this->rooms_count . "к.кв.";

    }

    /* метод для рендеринга floors */
    public function renderFloors()
    {
        return $this->floor . "/" . $this->floorcount;
    }


    /* метод для рендеринга house_type */
    public function renderHouse_type()
    {
        switch ($this->house_type) {
            case 2;
                {

                    return "кирп.";
                }
            case 1;
                {

                    return "пан.";
                }


            case 3;
                {

                    return "Монолит.";
                }
            case 4;
                {

                    return "Блочный";
                }
            case 5;
                {

                    return "Дер.";
                }


            default:
                {

                    return '';
                }


        }
    }

    /* метод для рендеринга areas */
    public function renderAreas()
    {
        $area_string = $this->grossarea;
        if ($this->living_area != 0) $area_string .= "/" . $this->living_area;
        if ($this->kitchen_area != 0) $area_string .= "/" . $this->kitchen_area;
        $area_string .= "м2";
        return $area_string;
    }

    /* метод для рендеринга phone */
    public function renderPhone()
    {
        if ($this->phone1) {
            $phone = Renders::phoneToString($this->phone1);
            if ($this->phone2) $phone .= "<br>" . Renders::phoneToString($this->phone2);
        } else $phone = 'no phone';
        return $phone;
    }

    /* метод для рендеринга phone */
    public function getDays_ago()
    {

        return " <div class='text-primary'>" . round(((time() - $this->date_start) / 86400), 0) . " дн. назад </div>";

    }

    public function createSimilar(array $similars_ids_all, array $similars_ids)
    {

        /* @var $similar SaleSimilar */

        if ($similars_ids_all) {
            $similar = $this->tryToFindSimilar($similars_ids_all);
            if ($similar) {
                if ($similar->moderated == 3) echo span("ОБЪЕКТ ПРОМОДЕРИРОВАН", 'primary');
                info('ExistedSimilar', 'danger');
                $similar->inject($this);
                $similar->generateIdSources();
                $similar->save();
                $this->id_similar = $similar->id;
            }


        } else {
            info("CREATING NEW SALE SIMILAR", 'primary');
            $similar = new SaleSimilar();
            if (!empty($similars_id)) $similar->similar_ids = Methods::convertToStringWithBorders($similars_id);
            if (!empty($similars_ids_all)) $similar->similar_ids_all = Methods::convertToStringWithBorders($similars_ids_all);
            $common_tags = $similar->collectTags($this->tags);
            echo "<br> ПРИСВОИЛИ TAGS <br>" . TagsWidgets::widget(['tags' => $common_tags]);
            $similar->generateIdSources();
            if (!$similar->save()) my_var_dump($similar->getErrors());
            else {
                $this->id_similar = $similar->id;

            }
            // проставляем, что нашли похожие
            if ($similars_ids_all) {
                foreach ($similars_ids_all as $similar_id) {
                    // Sale
                    $sale = Sale::findOne($similar_id);
                    if ($sale) {
                        $sale->id_similar = $similar->id;
                        $sale->save();
                        echo span('ПРОСТАВИЛИ ID_SIMILAR SALE') . "<br>";
                        //  echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                    }
                    //  Synchronization
                    $sale = Synchronization::findOne($similar_id);
                    if ($sale) {
                        $sale->id_similar = $similar->id;
                        $sale->save();
                        echo span('ПРОСТАВИЛИ ID_SIMILAR SYNC') . "<br>";
                        //   echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                    }

                }
            }


        }
//        // временный код
//        $ToChangeSales = Synchronization::find()->where(['in', 'id', $similars_ids_all])->all();
//        foreach ($ToChangeSales as $changeSale) {
//            $changeSale->id_similar = $id;
//           if (!$changeSale->save()) my_var_dump($changeSale->getErrors());
//
//
//        }
//        // временный код
//        $ToChangeSales = Sale::find()->where(['in', 'id', $similars_ids_all])->all();
//        foreach ($ToChangeSales as $changeSale) {
//            $changeSale->id_similar = $id;
//            $changeSale->save();
//            if (!$changeSale->save()) my_var_dump($changeSale->getErrors());
//        }


    }

    public function createSimilar1(array $similars_ids_all, array $similars_ids)
    {
        $ExistedSimilar = $this->tryToFindSimilar($similars_ids_all);
        if ($ExistedSimilar) {
            $similar_ids_all = preg_replace("/," . $this->id . ",/", ",", $ExistedSimilar->similar_ids_all);
            $similar_ids = preg_replace("/," . $this->id . ",/", ",", $ExistedSimilar->similar_ids);
            if (($similar_ids_all != $ExistedSimilar->similar_ids_all) OR ($similar_ids != $ExistedSimilar->similar_ids)) {
                info(" УДАЛИЛИ РАНЕЕ ПРИСВОЕННЫЙ SALESIMILAR");
            }
            $ExistedSimilar->similar_ids_all = $similar_ids_all;
            $ExistedSimilar->similar_ids = $similar_ids;
            if (!$ExistedSimilar->save()) my_var_dump($ExistedSimilar->getErrors());

        }
        info("CREATING NEW SALE SIMILAR", 'primary');


        $similar = new SaleSimilar();
        if (!empty($similars_id)) $similar->similar_ids = Methods::convertToStringWithBorders($similars_id);
        if (!empty($similars_ids_all)) $similar->similar_ids_all = Methods::convertToStringWithBorders($similars_ids_all);
        $common_tags = $similar->collectTags($this->tags);
        echo "<br> ПРИСВОИЛИ TAGS <br>" . TagsWidgets::widget(['tags' => $common_tags]);
        if (!$similar->save()) my_var_dump($similar->getErrors());
        else {
            $this->id_similar = $similar->id;
            $id = $similar->id;
        }
        // проставляем, что нашли похожие
        if ($similars_ids_all) {
            foreach ($similars_ids_all as $similar_id) {
                // Sale
                $sale = Sale::findOne($similar_id);
                if ($sale) {
                    $sale->id_similar = $id;
                    $sale->save();
                    echo span('ПРОСТАВИЛИ ID_SIMILAR SALE') . "<br>";
                    //  echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                }
                //  Synchronization
                $sale = Synchronization::findOne($similar_id);
                if ($sale) {
                    $sale->id_similar = $id;
                    $sale->save();
                    echo span('ПРОСТАВИЛИ ID_SIMILAR SYNC') . "<br>";
                    //   echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                }

            }
        }


//        // временный код
//        $ToChangeSales = Synchronization::find()->where(['in', 'id', $similars_ids_all])->all();
//        foreach ($ToChangeSales as $changeSale) {
//            $changeSale->id_similar = $id;
//           if (!$changeSale->save()) my_var_dump($changeSale->getErrors());
//
//
//        }
//        // временный код
//        $ToChangeSales = Sale::find()->where(['in', 'id', $similars_ids_all])->all();
//        foreach ($ToChangeSales as $changeSale) {
//            $changeSale->id_similar = $id;
//            $changeSale->save();
//            if (!$changeSale->save()) my_var_dump($changeSale->getErrors());
//        }


    }


    public function tryToFindSimilar(array $similars_id)
    {
        $query = SaleSimilar::find();
        if ($similars_id) {
            foreach ($similars_id as $similar_id) {
                $query->orWhere(['like', 'similar_ids_all', "," . $similar_id . ","]);
            }
        }
        return $query->one();


    }


    public function searchSimilar($type = 0, $select = 'id', $order_by = 'price')
    {
        if ($this->is_full_salesimilar()) {
            $query = Synchronization::find()
//                ->from(['s' => Synchronization::tableName()])
//                ->joinWith(['agent AS agent'])
//                ->joinWith(['addresses AS address'])
//                ->joinWith(['similarNew AS sim'])
                ->select($select)
                ->where(['floor' => $this->floor])
                ->andwhere(['rooms_count' => $this->rooms_count])
                //  ->andwhere(['<>','id' , $this->id])
                ->andwhere(['id_address' => $this->id_address])
                // ->andwhere(['<>', 'id_address', null])
                // ->andWhere(['not in', 'disactive', [1, 2]])
                ->andFilterWhere(['>=', 'grossarea', ($this->grossarea * (1 - SaleSimilar::SALE_SIMILAR_AREA_DIVERGANCE / 100))])
                ->andFilterWhere(['<=', 'grossarea', ($this->grossarea * (1 + SaleSimilar::SALE_SIMILAR_AREA_DIVERGANCE / 100))]);
            if ($this->price) {
                $query->andWhere(['>=', 'price', ($this->price * (1 - SaleSimilar::SALE_SIMILAR_PRICE_DIVERGANCE / 100))]);
                $query->andWhere(['<=', 'price', ($this->price * (1 + SaleSimilar::SALE_SIMILAR_PRICE_DIVERGANCE / 100))]);
            }
            if ($type == SaleSimilar::PUBLIC_TYPE) {
                info(" SEARCHING FOR PUBLICT IDs");
                $query->groupBy(['price', 'disactive', 'phone1', 'id_sources']);
            } else info(" SEARCHING FOR ALL IDs");
            $ids_sale = $query
                ->orderBy($order_by)
                ->column();
        } else return null;


        return $ids_sale;
    }

    public function analyseSimilar($type = 0)
    {
        if ($this->is_full_salesimilar()) {
            $query = $this->getQuerySimilar()->select('s.id,s.id_similar');
            return $query->all();


            //  $query->indexBy('id_similar');


        } else return false;


    }

    public function calculateSimilarObjects()
    {

        $query = $this->getQuerySimilar();
        return $ids_sale = $query
            ->groupBy('phone1,id_sources,disactive,agent.person_type')
            ->orderBy('price,disactive')
            ->all();

    }

    public function getQuerySimilar()
    {
        return Synchronization::find()
            ->from(['s' => Synchronization::tableName()])
            ->joinWith(['agent AS agent'])
//                ->joinWith(['addresses AS address'])
            //   ->joinWith(['similar AS sim'])
            // ->select('id,id_similar')
            ->where(['s.floor' => $this->floor])
            ->andwhere(['s.rooms_count' => $this->rooms_count])
            //  ->andwhere(['<>','id' , $this->id])
            ->andwhere(['s.id_address' => $this->id_address])
            // ->andwhere(['<>', 'id_address', null])
            //  ->andWhere(['not in', 'disactive', [1, 2]])
            ->andFilterWhere(['>=', 's.grossarea', ($this->grossarea * (1 - SaleSimilar::SALE_SIMILAR_AREA_DIVERGANCE / 100))])
            ->andFilterWhere(['<=', 's.grossarea', ($this->grossarea * (1 + SaleSimilar::SALE_SIMILAR_AREA_DIVERGANCE / 100))]);
        if ($this->price) {
            $query->andWhere(['>=', 's.price', ($this->price * (1 - SaleSimilar::SALE_SIMILAR_PRICE_DIVERGANCE / 100))]);
            $query->andWhere(['<=', 's.price', ($this->price * (1 + SaleSimilar::SALE_SIMILAR_PRICE_DIVERGANCE / 100))]);
        }

    }


    // связь с похожими
    public function getSimilarNew()
    {
        return $this->hasOne(SaleSimilar::className(), ['id' => 'id_similar']);
    }

    public function getCommon_tags()
    {
        return $this->hasOne(SaleSimilar::className(), ['id' => 'id_similar']);
    }

    public function getSimilar()
    {
        return $this->hasOne(SaleSimilar::className(), ['id' => 'id_similar']);
    }

    public function getSimilarsales()
    {

         if ($this->id_similar) return Sale::find()
            ->from(['s' => Sale::tableName()])
            ->joinWith(['agent AS agent'])
            ->joinWith(['addresses AS address'])
            ->joinWith(['similar AS sim'])
           //  ->select(SaleSimilar::SELECT_FIELDS)
            ->Where(['s.id_similar' => $this->id_similar])
            ->andWhere(['<>', 's.id', $this->id])
            ->all();
         else return false;

    }


    /* метод для рендеринга long_title */
    public function renderLong_title()
    {

        return $this->renderRooms_count() . "<strong> " . $this->renderAddress() . "</strong> , " . $this->renderFloors() . ", " . $this->renderHouse_type() . " , " . $this->renderAreas() . " цена: " . \app\models\Renders::Price($this->price);

    }

    public function getInform_message()
    {
        return strip_tags($this->renderLong_title() . "\r\n " . $this->description . "r\n " . $this->phone1);

    }

    /* метод для рендеринга статистики */
    public function renderStat()
    {
        // формируем статистику
        $sale_stat = "<i class=\"fa fa-map-marker\" aria-hidden=\"true\"></i> " . number_format($this->average_price_same, 0, ".", ".") . " (" . $this->average_price_same_count . ")";
        $sale_stat .= "<br><i class=\"fa fa-building-o\" aria-hidden=\"true\"></i> " . number_format($this->average_price, 0, ".", ".") . " (" . $this->average_price_count . ")";
        $sale_stat .= "<br><i class=\"fa fa-thumb-tack\" aria-hidden=\"true\"></i> " . number_format($this->average_price_address, 0, ".", ".") . " (" . $this->average_price_address_count . ")";
        return $sale_stat;
    }

    /*
     * метод для показа сколько у данного телефона объявлений
     */

    public function getCount_ads()
    {
        return $this->hasOne(Agents::className(), ['phone' => 'phone1'])->count_ads();
    }

    public function getAgent()
    {
        return $this->hasOne(Agents::className(), ['phone' => 'phone1']);
    }

    public function getTitle_to_copy()
    {
        $session = Yii::$app->session;
        $phone = $session->get('phone');
        $phone = explode(",", $phone);
        return $this->renderLong_title() . " " . Tags::render($this->tags, ',') . ", " . " телефон: " . $phone[0];
    }

    public function getTitle_to_copy_all()
    {
        $session = Yii::$app->session;
        $phone = $session->get('phone');
        $phone = explode(",", $phone);
        return $this->renderLong_title() . " " . Tags::render($this->tags, ',');
    }


    public function getPhotos()
    {

        if (count(unserialize($this->images)) > 0) {
            // my_var_dump($this->images);
            $list_of_images = unserialize($this->images);
            return "<img src='" . $list_of_images[0] . "' width='100'  height='100' style='position: absolute; clip: rect(0, 90px, 90px, 0);'>";

        } else {
            $url = Url::to('/images/nophoto.png', true);
            return "<img src=\"" . $url . "\" width='100' height='100' title='no photo' style='position: absolute; clip: rect(0, 90px, 90px, 0);'>";

        }
    }

    public function geocodate()
    {
        $geocodation = new Geocodetion();
        $geocodation->model = $this;
        $geocodation->TODO('sale');

        // импортируем
        $this->id_address = $geocodation->id_address;
        $this->geocodated = $geocodation->geocodated;
        if (Yii::$app->controller->id == 'my-debug') echo Yii::$app->view->render('@app/views/my-debug/_yandex_request', ['geocodation' => $geocodation]);

    }

    public function renderPerson($type = 'default')
    {
        if ($this->agent->status == 2) return $this->agent->person; else  return $this->person;

    }

    public function getAddresses()
    {
        {
            return $this->hasOne(Addresses::className(), ['id' => 'id_address']);
        }

    }

    public function renderAddress($type = 'default')
    {
        if ($type == 'mini') {
            if ($this->id_address) return "<div style='font-size:  0.7rem'>" . $this->addresses->address . "</div>";
            else return "<div style='color: grey;font-size:  0.7rem'>" . $this->address . "</div>";
        } else {
            if ($this->id_address) return "<div style='font-size:  0.9rem'>" . $this->addresses->address . "</div>";
            else return "<div style='color: grey;font-size:  0.9rem'>" . $this->address . "</div>";
        }

    }


    public function renderContacts($type = 'default')
    {
        if ($this->agent->person_type == 1) {
            $color = 'danger';
            $fa = "<i class=\"fa fa-user-secret d-none d-lg-block d-xl-none\" aria-hidden=\"true\"></i>";
            $title = "title = \"Агент (" . $this->agent->count_ads . ")\"";
        } else {

            $fa = "<i class=\"fa fa-user text-primary d-none d-lg-block d-xl-none\" aria-hidden=\"true\"></i>";
            $color = 'primary';
            $title = "title = \"Собственник\"";
        }
        $body = "<div  " . $title . " style=\"text-align: center;\">";

        if ($type == 'mini') {
            $body .= "<div style=\"font-size:  0.9rem\">" . $this->renderPerson() . "<span class=\"badge badge-pill pink\">" . $this->agent->count_ads . "</span></div>";
            //$body .= "<h6>" . $fa . "<span class=\"badge badge-" . $color . "\">" . $this->renderPhone() . "</span></h6>";
            $body .= Html::a("<h6><span class=\"badge badge-" . $color . "\">" . $this->renderPhone() . "</span></h6>","tel:".$this->renderPhone());
        } elseif ($type == 'map') {
            $body .= "<span class=\"badge badge-" . $color . "\">" . $this->renderPhone() . "</span>";

        } else {
            $body .= "<h6>" . $this->renderPerson() . "</h6>";
            $body .= "<h4>" . $fa . Html::a("<span class=\"badge badge-" . $color . "\">" . $this->renderPhone() . "</span></h4>","tel:".$this->renderPhone());
        }
        $body .= "</div>";

        return $body;


    }

    public function IsInTemplate($template)
    {
        if ($template instanceof SaleFiltersOnControl) {
            if (($this->id_address == $template['id_address'])
                and ($this->rooms_count == $template['rooms_count'])
                and ($this->floor == $template['floor'])
                and ($this->grossarea < $template['grossarea'] * (100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100)
                and ($this->grossarea > $template['grossarea'] * (100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100)
                and ($this->price >= $template['price'])
            )
                return true;
        }
        if ($template instanceof SaleFiltersOnBlack) {
            if (($this->id_address == $template['id_address'])
                and ($this->rooms_count == $template['rooms_count'])
                and ($this->floor == $template['floor'])
                and ($this->grossarea < $template['grossarea'] * (100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100)
                and ($this->grossarea > $template['grossarea'] * (100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100)
            )
                return true;
        }


    }

    public
    function AutoLoadTags($highlighting = false)
    {
        $tags = [];
        //    $Patterned_Tags = Tags::find()->where(['<>', 'patterns', ''])->all();

        if (empty(Yii::$app->cache->get('patterned_tags'))) {
            Yii::$app->cache->set('patterned_tags', Tags::find()->where(['<>', 'patterns', ''])->all());

        }
        // здесь мы просто определяем теги которые есть
        $Patterned_Tags = Yii::$app->cache->get('patterned_tags');
        //  echo "<hr> есть " . count($Patterned_Tags) . " для автозагрузки Tags";
        //  echo "<br><h4>".$this->id."</h4> deszcription = ".$this->description;

        foreach ($Patterned_Tags as $tag) {
            $array_of_patterns = explode('&', $tag->patterns);
            foreach ($array_of_patterns as $pattern) {
                if (preg_match("/" . $pattern . "/iu", $this->description, $output)) {
                    //   echo "........." .Renders::Highlighting($this->description, $output[0], 10);
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
            if ($highlighting) {

                // replacing
                $body = $this->description;
                foreach (Tags::find()->where(['in', 'id', array_unique($tags)])->all() as $tag) {
                    $array_of_patterns = explode('&', $tag->patterns);
                    foreach ($array_of_patterns as $pattern) {
                        if (preg_match("/(" . $pattern . ")/iu", $this->description, $output)) {
                            $body = str_replace($output[1], Renders::Highlighting($body, $output[1], 0), $body);

                            //  echo "<br> нашли<span class=\"badge badge-success\">".$output[0]."</span> AS <span class=\"badge badge-indigo\">".$pattern."</span>";
                            // array_push($tags, $tag->id);
                            break;
                        }// else echo "<span class=\"badge badge-warning\">".$pattern."</span>";

                    }
                }
                $body = preg_replace("/<my_selection_end>.{100,}<my_selection_start>/xuU", "<my_selection_end>    <my_selection_start>", $body);;
                echo $body = "<small>".preg_replace("/<my_selection_end>.{150,}$/uU", "<my_selection_end>    ", $body)."</small>";

            }


            $this->setAutoloadedTags($tags);
            return $tags;
        } else {
            $this->tags_id = '';
            return [];
        }
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

    public function similarCheck()
    {
        /*

         */
        info("PROCESSING ... similarCheck");
        $similar_ids_all = $this->searchSimilar();
        $public_ids = $this->searchSimilar(SaleSimilar::PUBLIC_TYPE);
        if (!$similar_ids_all) {
            $this->id_similar = null;
            return false;

        }


        info(" SALE_SIMILAR_IS_EXISTS ");
        info("SALE_SIMILARS = '" . my_implode($similar_ids_all) . "'" . " count = " . count($similar_ids_all), 'alert');
        info("SALE_SIMILARS_public = '" . my_implode($public_ids) . "'" . " count = " . count($public_ids), 'alert');
        $ExistedSimilar = $this->tryToFindSimilar($similar_ids_all);
        if ($ExistedSimilar) {
            info('SaleSimilar WAS CREATED BEFORE....Modifying', 'danger');
            info('Modifying....', 'primary');
            echo Html::a("id=" . $ExistedSimilar->id, Url::to(['sale-similar/view', 'id' => $ExistedSimilar->id]), ['target' => '_blank']) . "<br>";
            if ($ExistedSimilar->moderated == SaleSimilar::MODERATED) echo span("ОБЪЕКТ ПРОМОДЕРИРОВАН", 'primary');
            $ExistedSimilar->updateIds();
            $ExistedSimilar->save();

        } else {
            info('SaleSimilar WAS NOT CREATED BEFORE....', 'danger');
            info('SaleSimilar CREATING', 'success');
            $this->createSimilar($similar_ids_all, $public_ids);

        }

        // проставляем, что нашли похожие
        $not_marked_sales = Sale::find()->select('id')->where(['in', 'id', Methods::convertToArrayWithBorders($similar_ids_all)])->column();
        if ($not_marked_sales) {
            foreach ($not_marked_sales as $similar_id) {
                // Sale
                $sale = Sale::findOne($similar_id);
                if ($sale) {
                    $sale->id_similar = $ExistedSimilar->id;
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                    echo span('ПРОСТАВИЛИ ID_SIMILAR SALE') . "<br>";
                    //  echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                }
                //  Synchronization
                $sale = Synchronization::findOne($similar_id);
                if ($sale) {
                    $sale->id_similar = $ExistedSimilar->id;
                    if (!$sale->save()) my_var_dump($sale->getErrors());
                    echo span('ПРОСТАВИЛИ ID_SIMILAR SYNC') . "<br>";
                    //   echo Renders::StaticView('sale/_sale-table', ['model' => $sale]);

                }

            }
        }
        // $ExistedSimilar->inject($this);


//        $similar_sales = Synchronization::find()
//            ->where(['in', 'id', $public_ids])
//            ->orderBy('price')
//            ->all();
        //  echo Renders::StaticView('_chart_prices', ['prices' => $this->searchSimilar(0,'price'), 'labels' => $this->searchSimilar(0, 'id') ]);
        //  echo Renders::StaticView('mini_sale_similar', ['sales' => $similar_sales, 'contacts' => true, 'controls' => true]);

    }

    public function similarCheckNewer()
    {
        info("IS_ID_SIMILAR");
        $similarReport = $this->analyseSimilar();
        if ($similarReport === false) {
            info(" CANNOT ANALYSE FOR SALESIMILAR OBJECT", DANGER);
            $this->id_similar = null;
            $this->save();
            return false;
        } else {
            $is_existed_salesimilar = array_filter($similarReport, function ($sale) {
                return $sale['id_similar'];
            });
            if ($is_existed_salesimilar) {
                $array = [];
                foreach ($is_existed_salesimilar as $item) {
                    $array[] = $item['id_similar'];
                }
                $is_existed_salesimilar = array_unique($array);
            }

            info("EXISTED_SALESIMILAR_ID COUNT=" . count($is_existed_salesimilar));
            my_var_dump($is_existed_salesimilar);

            $not_is_existed_salesimilar = array_filter($similarReport, function ($sale) {
                return ($sale['id_similar'] == 0);
            });
            info("NOT_EXISTED_SALESIMILAR_ID COUNT=" . count($not_is_existed_salesimilar), DANGER);

            if ((count($is_existed_salesimilar) == 1)) {
                info(" THERE IS ONE EXISTED SIMILAR , GETTING HIS ID_SIMILAR", SUCCESS);
                $id_similar = $is_existed_salesimilar[0];


                //  my_var_dump($is_existed_salesimilar);

            } elseif ((count($is_existed_salesimilar) == 0)) {
                info(" THERE IS NO ONE EXISTED SIMILAR , CREATING NEW", PRIMARY);
                $new_similar = new SaleSimilar();
                if (!$new_similar->save()) my_var_dump($new_similar->errors);
                $id_similar = $new_similar->id;
            } else {
                info("THERE ARE AT LEAST TWO SIMILARS VARIANTS", DANGER);
                Notifications::VKMessage(" СПОРТНЫЙ МОМЕНТ ПО SALESIMILAR id=" . $this->id);
                $id_similar = $is_existed_salesimilar[0];
            }
// если установили или создали новый id_similar то проставляем по всем его
            if ($id_similar) {
                $this->id_similar = $id_similar;
                if ($not_is_existed_salesimilar) {
                    info("SETTING FOR NOT EXISTED FOR " . count($not_is_existed_salesimilar) . " ITEMS", PRIMARY);
                    foreach ($not_is_existed_salesimilar as $item) {
                        // обновляем в облаке
                        Sale::updateAll(['id_similar' => $id_similar], ['id' => $item->id]);
                        $item->id_similar = $id_similar;
                        if (!$item->save()) my_var_dump($item->errors);
                    }
                }
            }


            //  my_var_dump($is_existed_salesimilar);

            // my_var_dump(count($similarReport));
            //  $similars = $this->calculateSimilarObjects();
            info(" COUNT=" . count($similars));

            //  foreach ($similars as $similar) {
            //   echo \Yii::$app->view->render('@app/views/sale/_mini_sale_similar', ['sales' => $similars, 'contacts' => true]);
            //   echo "<br>".$similar->renderLong_title().Html::a('url',$similar->url);
            // }

        }
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

        // info(Tags::convertToString($tags));
        $this->tags_id = Tags::convertToString($tags);
    }

}
