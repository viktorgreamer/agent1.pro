<?php

namespace app\models;

use app\models\SalefiltersModels\SaleFiltersOnBlack;
use SebastianBergmann\CodeCoverage\Report\Xml\Method;
use Yii;
use yii\helpers\ArrayHelper;
use app\models\SalefiltersModels\SaleFiltersOnControl;

use Eventviva\ImageResize;

/**
 * This is the model class for table "Velikiy_Novgorod_sale_filters".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $period_ads
 * @property string $name
 * @property string $rooms_count
 * @property integer $house_type
 * @property string $locality
 * @property string $district
 * @property string $text_like
 * @property string $polygon_text
 * @property integer $price_down
 * @property integer $price_up
 * @property integer $grossarea_down
 * @property integer $grossarea_up
 * @property integer $status_blacklist2
 * @property boolean $agents
 * @property boolean $housekeepers
 * @property integer $date_of_ads
 * @property integer $floor_down
 * @property integer $floor_up
 * @property integer $floorcount_down
 * @property integer $floorcount_up
 * @property boolean $not_last_floor
 * @property integer $sort_by
 * @property string $black_list_id
 * @property string $similar_white_list_id
 * @property string $similar_black_list_id
 * @property string $processed_ids
 * @property string $white_list_id
 * @property string $check_list_id
 * @property boolean $mail_inform
 * @property integer $sms_inform
 * @property integer $is_super_filter
 * @property integer $discount
 * @property integer $date_start
 * @property integer $date_finish
 * @property string $phone
 * @property integer $year_up
 * @property integer $year_down
 * @property string $id_sources
 * @property string $id_address
 * @property integer $type
 * @property integer $uniqueness
 * @property string $komment
 * @property string $hidden_comment
 * @property string $tags_id
 * @property string $minus_id_addresses
 * @property string $regions
 * @property string $plus_tags
 * @property string $minus_tags
 * @property string $relevanted_ids
 */
class SaleFilters extends \yii\db\ActiveRecord
{
    // public $relevanted_ids;
    /**
     * @inheritdoc
     */

    const ORDER_BY = [0 => 'id', 2 => 'цена', 1 => 'время', 3 => 'адресам'];

    const DEFAULT_PERIODS_ARRAY = [
        0 => 'Любой',
        1 => '1д',
        2 => '2д',
        3 => '3д',
        7 => '7д',
        14 => '14д',
        31 => '31д',
        90 => '3м'

    ];
    const TYPE_OF_OBJECTS_ARRAY = [
        0 => 'Любой',
        1 => '1к',
        2 => '2к',
        3 => '3к',
        4 => '4к',
        5 => '5+',
        20 => 'Студии',
        30 => 'Комнаты'
    ];
    const TYPE_OF_FILTERS_ARRAY = [
        0 => 'Любой',
        1 => 'Клиент',
        2 => 'Публичный',
        3 => 'Аггрегатор',
        4 => 'Другой',
        5 => 'Район',
        6 => 'Проданные',
        7 => 'АГЕНТ',
        8 => 'РЕКЛАМНЫЙ АГГРЕГАТОР',
        9 => 'ОТЛАДОЧНЫЙ'

    ];
    const TYPE_OF_FILTERS_ARRAY_PUBLIC = [
        0 => 'Любой',
        1 => 'Клиент',
        2 => 'Публичный',
        3 => 'Аггрегатор',
        7 => 'АГЕНТ',

    ];
    const CLIENT_TYPE = 1;
    const PUBLIC_TYPE = 2;
    const AGENT_TYPE = 7;
    const AGGRERATOR_TYPE = 3;
    const PERSENTAGE_OF_AREA_DIVERGENSCE = 10;

    // types_of_show
    const SHOW_MAIN = 0;
    const SHOW_WHITE = 1;
    const SHOW_BLACK = 2;
    const SHOW_SIMILAR_WHITE = 3;
    const SHOW_SIMILAR_BLACK = 4;
    const SHOW_ON_CONTROLS = 5;
    const SHOW_PROCESSED = 6;
    const  TYPE_OF_SHOW_ARRAY = [
        SaleFilters::SHOW_MAIN => 'ОБЩИЙ',
        SaleFilters::SHOW_WHITE => 'ОТОБРАННЫЕ',
        SaleFilters::SHOW_BLACK => 'ЧЕРНЫЙ СПИСОК',
        SaleFilters::SHOW_SIMILAR_WHITE => 'БЕЛЫЙ СПИСОК ПОХОЖИЕ',
        SaleFilters::SHOW_SIMILAR_BLACK => 'ЧЕРНЫЙ СПИСОК ПОХОЖИЕ',
        SaleFilters::SHOW_ON_CONTROLS => 'НА КОНТОЛЕ ПО ЦЕНЕ',
        SaleFilters::SHOW_PROCESSED => 'ОБРАБОТАННЫЕ',

    ];

    // type_of_sorting
    const SORTING_PRICE_ASC = 0;
    const SORTING_ID = 3;
    const SORTING_DATE_START_ASC = 1;
    const SORTING_DATE_START_DESC = 2;
    const SORTING_PRICE_DESC = 4;
    const SORTING_ID_ADDRESS_ASC = 5;
    const SORTING_ID_ADDRESS_DESC = 6;
    const TYPE_OF_SORTING_ARRAY = [
        SaleFilters::SORTING_PRICE_ASC => "Цена &uarr;",
        SaleFilters::SORTING_ID => 'ID &uarr;',
        SaleFilters::SORTING_DATE_START_ASC => 'Дата &uarr;',
        SaleFilters::SORTING_DATE_START_DESC => 'Дата &darr;',
        SaleFilters::SORTING_PRICE_DESC => 'Цена &darr;',
        SaleFilters::SORTING_ID_ADDRESS_ASC => 'Адрес &uarr;',
        SaleFilters::SORTING_ID_ADDRESS_DESC => 'Адрес &darr;',

    ];

    // type_of_unique
    const UNIQUE_MAIN = 0;
    const UNIQUE_ROW = 1;
    const UNIQUE_OBJECT = 2;
    const TYPE_OF_UNIQUING = [
        0 => 'Нет',
        1 => 'Да',
        2 => 'EXTRA',
    ];

    // type_of_view
    const VIEW_LIST = 0;
    const VIEW_MAP = 1;
    const  TYPE_OF_VIEWS = [
        0 => 'Список',
        1 => 'Карта'
    ];
    public static function mapBalcon() {
        return [
            0 => "Неважно",
            1 => "Балкон",
            2 => "Лоджия",
            3 => "Балк/Лодж",
        ];
    }


    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_filters';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_filters";
        }
    }


    public function modifyExtendedParams()
    {
        foreach (['sort_by', 'uniqueness', 'sale_disactive'] as $param) {
            {
                if (isset($_GET[$param])) $this[$param] = $_GET[$param];
            }

        }
    }


    // производит сортировку по количеству одиниковых tags

    public function getRelevantedByTags($limit = 5, $type = 'public')
    {
        $tags = explode(",", $this->tags_id);
        // if ($type == 'public-sold') $SaleLists = SaleLists::find()->where(['type' => 6])->all();
        $Salefilters = SaleFilters::find()->where(['type' => SaleFilters::AGGRERATOR_TYPE])->all();
        $tags_array_counter = [];
        foreach ($Salefilters as $salefilter) {
            $n = 0;
            foreach ($tags as $tag) {
                if (in_array($tag, explode(",", $salefilter->tags_id))) $n++;
            }
            $tags_array_counter[$salefilter->id] = $n;

        }
        arsort($tags_array_counter);
        // my_var_dump($tags_array_counter);
        $n = 0;
        $tags = [];
        foreach ($tags_array_counter as $key => $count) {
            array_push($tags, $key);
            if ($n == $limit) break;
            $n++;
        }
        // my_var_dump(array_slice($tags_array_counter,0,$limit));
        // my_var_dump(array_keys(array_slice($tags_array_counter,0,$limit)));
        return $tags;
    }


    public function init()
    {
        parent::init();
        if (!isset($this->period_ads)) $this->period_ads = 0;
    }

    public function beforeSave($insert)

    {
        $this->time = time();

        //   info('beforeValidate');

        if (is_array($this->rooms_count)) {
            $this->rooms_count = implode(',', $this->rooms_count);
        }
         if (is_numeric($this->rooms_count)) {
            $this->rooms_count = $this->rooms_count;
        }

        if (is_array($this->id_sources)) {
            $this->id_sources = implode(',', $this->id_sources);
        }
        if (is_array($this->disactive_id_sources)) {
            $this->disactive_id_sources = implode(',', $this->disactive_id_sources);
        }
        if (is_array($this->id_address)) {
            $this->id_address = implode(',', $this->id_address);
        }
        if ($this->agents) $this->agents = 1;
        if ($this->housekeepers) $this->housekeepers = 1;
        if ($this->not_last_floor) $this->not_last_floor = 1;


        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();
        if (is_string($this->rooms_count)) $this->rooms_count = explode(',', $this->rooms_count);
        if (is_string($this->id_sources)) $this->id_sources = explode(',', $this->id_sources);

        $this->disactive_id_sources = Methods::convertToArray($this->disactive_id_sources);


        if ($this->id_address) {
           // info("id addsess '".$this->id_address."'");
            $this->id_address = explode(',', $this->id_address);
        }



    }


    public function getRooms()
    {
        return explode(",", $this->rooms_count);
    }

    public function setRooms($insert)
    {
        if (is_array($insert)) $this->rooms_count = implode(',', $insert);

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

    public static function getRegions()
    {
        return ArrayHelper::map(Self::find()->where(['type' => 5])->all(), 'id', 'name');
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'uniqueness', 'user_id', 'period_ads', 'price_down', 'price_up', 'grossarea_down', 'grossarea_up', 'status_blacklist2', 'date_of_ads', 'floor_down', 'floor_up', 'year_down', 'year_up',
                'house_type', 'balcon', 'floorcount_down', 'floorcount_up', 'is_super_filter', 'type', 'discount', 'sort_by', 'date_start', 'date_finish', 'discount', 'type', 'sale_disactive', 'moderated'], 'integer'],
            [['polygon_text', 'text_like', 'similar_white_list_id', 'similar_black_list_id', 'black_list_id', 'white_list_id', 'check_list_id', 'phone', 'komment', 'hidden_comment', 'plus_tags', 'minus_tags'], 'string'],
            [['name', 'locality', 'tags_id', 'photo', 'regions', 'plus_tags'], 'string', 'max' => 255],
            [['rooms_count', 'id_sources', 'disactive_id_sources'], 'string', 'max' => 30],
            [['rooms_count', 'id_sources', 'polygon_text', 'plus_tags', 'minus_tags'], 'safe'],
            [['mail_inform', 'not_last_floor', 'agents', 'housekeepers'], 'boolean'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Агент',
            'period_ads' => 'Период',
            'type' => 'Тип',
            'name' => 'Название',
            'rooms_count' => 'Кол-во комнат',
            'polygon_text' => 'Polygon Text',
            'text_like' => 'text_like',
            'price_down' => 'Price Down',
            'price_up' => 'Price Up',
            'grossarea_down' => 'Grossarea Down',
            'grossarea_up' => 'Grossarea Up',
            'status_blacklist2' => 'Status Blacklist2',
            'date_of_ads' => 'Date Of Ads',
            'floor_down' => 'Floor Down',
            'floor_up' => 'Floor Up',
            'floorcount_down' => 'Floorcount Down',
            'floorcount_up' => 'Floorcount Up',
            'not_last_floor' => '',
            'sort_by' => 'Sort By',
            'black_list_id' => 'Black List ID',
            'is_client' => 'Клиент',
            'komment' => 'Комментарий',
            'hidden_comment' => 'Скрытый Комментарий',
            'tags_id' => 'Теги',
        ];
    }

    /*
     * данный метод создает из текущего фильтра список вариантов
     * */

    public function createSaleListFromSaleFilter()
    {
        // создаем новый список, делаем полные поиск без пагинации, и кидаем этот список id в новый список и сохраняем его
        $session = Yii::$app->session;
        $salelist = new SaleLists();
        $salelist->name = Yii::$app->request->get('salelist_name');
        $searchModel = new SaleSearch();
        $data2 = $searchModel->search_without_pagination($this);
        $list_of_ids = ArrayHelper::getColumn($data2['all_sales'], 'id');
        // var_dump($list_of_ids);
        $salelist->user_id = $session->get('user_id');
        $salelist->list_of_ids = implode(",", $list_of_ids);
        $session['current_list'] = $salelist;
        if ($salelist->Exists()) $session->setFlash('ExistedSaleList', true);
        elseif (!$salelist->save()) my_var_dump($this->getErrors());
        else return true;

    }


    public function Exists()
    {
        return SaleFilters::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['name' => $this->name])
            ->exists();
    }

    public function FindExisted()
    {
        $salefilter = SaleFilters::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['name' => $this->name])
            ->one();
        return $salefilter->id;
    }

    public function setTags($insert)
    {
        $this->tags_id = Tags::convertToString($insert);
    }

    public function getTags()
    {
        return Tags::convertToArray($this->tags_id);
    }

    // метод который дает истину если данное id входит в black_list_id или ни один дабликат Ирр не входит в этот длист


    /*
     * управляющие метод шаблона соответствия исходя и зараметров ['salefilter_id', 'rooms_count', 'id_address', 'floor', 'grossarea', 'price']
     * либо удаляет либо создает новый шаблон * */
    public function OnControl($id_similar, $price)
    {
        $is_Exists_price_more = SaleFiltersOnControl::find()
            ->where(['id_salefilter' => $this->id])
            ->where(['id_similar' => $id_similar])
            ->one();
        if ($is_Exists_price_more) {
            if ($is_Exists_price_more->price == $price) {
                $is_Exists_price_more->delete();
                return "Удалили шаблон";
            }
            $is_Exists_price_more->price = $price;
            $is_Exists_price_more->save();
            return "Обновили шаблон";
        } else return $this->createOnControl($id_similar, $price);


    }

    /*
     * метод просто ренед * */

    protected function renderOnControlTemplate($type, $template)
    {

        $body = '';
        $body .= Sale::ROOMS_COUNT_ARRAY[$template['rooms_count']] . " " . Addresses::findOne($template['id_address'])->address;
        $body .= " " . $template['floor'] . " эт . S < " . round($template['grossarea'] * ((100 + SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100));
        $body .= " > " . round($template['grossarea'] * ((100 - SaleFilters::PERSENTAGE_OF_AREA_DIVERGENSCE) / 100));
        if ($type == 'ONCONTROL') {
            $body .= "Ценою >=" . round($template['price']);
        }

        return $body;


    }

    /*
     * метод создает шаблон соответствия исходя и зараметров ['salefilter_id', 'rooms_count', 'id_address', 'floor', 'grossarea', 'price'] * */

    protected function createOnControl($id_similar, $price)
    {
        $OnControls = New SaleFiltersOnControl();
        $OnControls->id_similar = $id_similar;
        $OnControls->id_salefilter = $this->id;
        $OnControls->price = $price;
        $OnControls->date = time();
        $OnControls->save();
        return " Успешно создали шаблон";
    }


    public function add_to_check_list($id_item)
    {
        $new_Array = explode(",", $this->check_list_id);
        if (empty($this->check_list_id)) $this->check_list_id = $id_item; else {
            array_push($new_Array, $id_item);
            $this->check_list_id = implode(",", $new_Array);
        }
        $this->removeFromOtherLists($id_item, ['white', 'black']);

        $this->save();
    }

    public function add_to_black_list($id_item)
    {
        $new_Array = explode(",", $this->black_list_id);
        if (empty($this->black_list_id)) $this->black_list_id = $id_item; else {
            array_push($new_Array, $id_item);
            $this->black_list_id = implode(",", $new_Array);
        }
        $this->removeFromOtherLists($id_item, ['check', 'white']);

        $this->save();
    }

    /// устаревный метод
    public function removeFromOtherLists($id_item, $name_lists)
    {
        if (in_array('check', $name_lists)) {
            $new_Array = explode(",", $this->check_list_id);
            if ((!empty($this->check_list_id)) and (in_array($id_item, $new_Array))) {
                unset($new_Array[array_search($id_item, $new_Array)]);

            }
        }
        if (in_array('white', $name_lists)) {
            $new_Array = explode(",", $this->check_list_id);
            if ((!empty($this->white_list_id)) and (in_array($id_item, $new_Array))) {
                unset($new_Array[array_search($id_item, $new_Array)]);

            }
        }
        if (in_array('black', $name_lists)) {
            $new_Array = explode(",", $this->check_list_id);
            if ((!empty($this->black_list_id)) and (in_array($id_item, $new_Array))) {
                unset($new_Array[array_search($id_item, $new_Array)]);

            }
        }

    }



    public static function getMyFiltersAsArray($types = 0)
    {
        if (!$types) {
            $session = Yii::$app->session;
            $user_id = $session->get('user_id');
            return ArrayHelper::map(SaleFilters::find()->all(), 'id', 'name');
        } else {
            $session = Yii::$app->session;
            $user_id = $session->get('user_id');
            return ArrayHelper::map(SaleFilters::find()->andWhere(['in', 'type', $types])->all(), 'id', 'name');
        }

    }

    public function copyAttributes($salefilter)
    {
        $this->rooms_count = $salefilter->rooms_count;
        $this->district = $salefilter->district;
        $this->price_up = $salefilter->price_up;
        $this->price_down = $salefilter->price_down;
        $this->mail_inform = $salefilter->mail_inform;
        $this->locality = $salefilter->locality;
        $this->polygon_text = $salefilter->polygon_text;
        $this->komment = $salefilter->komment;
        $this->floor_up = $salefilter->floor_up;
        $this->floor_down = $salefilter->floor_down;
        $this->floorcount_down = $salefilter->floorcount_down;
        $this->floorcount_up = $salefilter->floorcount_up;
        $this->id_sources = $salefilter->id_sources;
        $this->grossarea_down = $salefilter->grossarea_down;
        $this->grossarea_up = $salefilter->grossarea_up;
        $this->text_like = $salefilter->text_like;
        $this->phone = $salefilter->phone;
        $this->not_last_floor = $salefilter->not_last_floor;
        $this->user_id = $salefilter->user_id;
        $this->agents = $salefilter->agents;
        $this->housekeepers = $salefilter->housekeepers;
        $this->house_type = $salefilter->house_type;
        $this->regions = $salefilter->regions;
        $this->year_down = $salefilter->year_down;
        $this->year_up = $salefilter->year_up;
        $this->type = $salefilter->type;
        $this->id_address = $salefilter->id_address;
        $this->moderated = $salefilter->moderated;
        $this->photo = $salefilter->photo;
        $this->sale_disactive = $salefilter->sale_disactive;
        $this->discount = $salefilter->discount;
        $this->sms_inform = $salefilter->sms_inform;
        $this->mail_inform = $salefilter->mail_inform;
        $this->plus_tags = $salefilter->plus_tags;
        $this->minus_tags = $salefilter->minus_tags;


    }

    /*
     * метод который позволяет отпределить попадает ли данных вариант под филльтр
     *
     * */
    public function Is_in_salefilter($sale)
    {
        if (!empty($this->price_up)) if ($sale->price > $this->price_up) return false;
        if (!empty($this->price_down)) if ($sale->price < $this->price_down) return false;
        if (!empty($this->year_down)) if ($sale->year < $this->year_down) return false;
        if (!empty($this->year_up)) if ($sale->year > $this->year_up) return false;
        if (!empty($this->grossarea_down)) if ($sale->grossarea < $this->grossarea_down) return false;
        if (!empty($this->grossarea_up)) if ($sale->grossarea > $this->grossarea_up) return false;
        if (!empty($this->floorcount_down)) if ($sale->floorcount < $this->floorcount_down) return false;
        if (!empty($this->grossarea_up)) if ($sale->floorcount > $this->floorcount_up) return false;
        if (!empty($this->floor_down)) if ($sale->floor < $this->floor_down) return false;
        if (!empty($this->floor_up)) if ($sale->floor > $this->floor_up) return false;

        // echo "<br> идет проверка salefilter";
        //  echo "<br> прошел основные параметры";
        // if ($this->sale_disactive != 10) if ($sale->disactive != $this->sale_disactive) return false;
        if (!empty($this->rooms_count)) if (!in_array($sale->rooms_count, $this->rooms_count)) return false;
        if (!empty($this->phone)) if (!in_array($sale->phone1, explode(",", $this->phone))) return false;
        if (!empty($this->house_type)) if (!in_array($sale->house_type, explode(",", $this->house_type))) return false;;
        if (!empty($this->id_sources)) if (!in_array($sale->id_sources, $this->id_sources)) return false;;
        if (!empty($this->id_address)) if (!in_array($sale->id_address, $this->id_address)) return false;;
        if (!empty($this->black_list_id)) if (in_array($sale->id, explode(",", $this->black_list_id))) return false;

//        echo "<br> прошел дополнительные параметры";
        if ($this->housekeepers) if ($sale->status_blacklist2 != 1) return false;
        if ($this->agents) if ($sale->status_blacklist2 != 0) return false;

        if ($this->text_like != '') {
            if ((!strpos(mb_strtolower($sale->address), mb_strtolower($this->text_like))) and
                (!strpos(mb_strtolower($sale->description), mb_strtolower($this->text_like)))
            ) return false;
        }
        // если поледний этаж
        if (($this->not_last_floor) and ($sale->floorcount == $sale->floor)) return false;
        // если попадает в полигон
        // echo $this->polygon_text;
        if (($this->polygon_text != '') and (!isPointInsidePolygon(json_decode(substr($this->polygon_text, 1, -1)), [$sale->coords_x, $sale->coords_y]))) return false;
        // сели супер фильтрр
        if ($this->discount != 0) {
            //echo "<br> идет проверка суперфилтра";
            if ($sale->average_price == 0) return false;
            elseif ($sale->price > ((100 - $this->discount) / 100 * $sale->average_price)) return false;
        }

        // если прошли фильтр, то пишем
        return "Появился НОВЫЙ ВАРИАНТ по адресу " . $sale->address . " " . $sale->RenderFloors() . " по цене" . $sale->price;
    }


    public function plus_tags_address()
    {
        if ($this->plus_tags) return Tags::find()->where(['in', 'id', explode(",", $this->plus_tags)])->andWhere(['in', 'type', [1, 2]])->select('id')->column();

    }

    public function plus_tags_sale()
    {
        if ($this->plus_tags)
            return Tags::find()->where(['in', 'id', explode(",", $this->plus_tags)])->andWhere(['not in', 'type', [1, 2]])->select('id')->column();

    }

    public function minus_tags_address()
    {
        if ($this->minus_tags) return Tags::find()->where(['in', 'id', explode(",", $this->minus_tags)])->andWhere(['in', 'type', [1, 2]])->select('id')->column();

    }

    public function minus_tags_sale()
    {
        if ($this->minus_tags)
            return Tags::find()->where(['in', 'id', explode(",", $this->minus_tags)])->andWhere(['not in', 'type', [1, 2]])->select('id')->column();

    }


    public function LoadPhotoToLocal()
    {

        $session = Yii::$app->session;
        $user_id = $session->get('user_id');

        $main_dir = "C:\\realty\\foto_" . $user_id . "_" . my_transliterate($this->name);
        $sales = Sale::find()
            ->from(['s' => Sale::tableName()])
            ->joinWith(['agent AS agent'])
            ->joinWith(['addresses AS address'])
            ->joinWith(['similarNew AS sim'])
            ->where(['in', 's.id', explode(",", $this->white_list_id)])
            // ->andwhere(['s.id_sources' => 2])
            ->limit(500)
            ->all();
        echo " всего объявлений" . count($sales);
        if (!file_exists($main_dir)) {
            echo " создаем директорию";
            mkdir($main_dir);
        }
        foreach ($sales as $sale) {
            echo $sale->renderSource();
            echo " <br>";
            echo $sale->id . " list of photo <a href='" . $sale->url . "'> link </a>";
            // echo " <br>";

            $links = unserialize($sale->images);

            if (count($links) > 0) {
                foreach ($links as $link) {
                    //  echo " <br>";
                    // echo $link;
                    $name = array_pop(explode("/", $link));
                    // echo " name = " . $name . " sale address" . $sale->address;
                    if ($sale->addresses) $generated_address = $sale->addresses->generateAddress("_");
                    else $generated_address = $sale->address;

                    // $dir = $main_dir . "/" . my_transliterate($sale->id . "_" . $sale->rooms_count . "_" . preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $sale->address));
                    $dir = $main_dir . "/" . ($sale->id . "_" . $sale->rooms_count . "_" . my_transliterate($generated_address));
                    //info(" КОДИРОВКА = ".mb_detect_encoding($sale->address)."    ". my_transliterate($sale->address));
                    $is_download = false;
                    //если такой директории еще не существует то создаем ее и
                    if (!file_exists($dir)) {
                        $n++;
                        echo " создаем директорию";
                        mkdir($dir);
                    }
                    if (!file_exists($dir . "/" . $name)) {
                        if ($name != '') {
                            if ($sale->id_sources == 3) $target_url = str_replace('640x480', '1280x960', $link);
                            else $target_url = $link;
                            info("Сохраняем фото " . $target_url);
                            $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:39.0) Gecko/20100101 Firefox/39.0';
                            $ch = curl_init($target_url);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
                            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
                            $output = curl_exec($ch);
                            if ($output) {
                                $fh = fopen($dir . "/" . $name, 'w');
                                fwrite($fh, $output);

                                if (!file_exists($dir . "/" . $name)) fwrite($fh, $output);

                                fclose($fh);
                                if (filesize($dir . "/" . $name) > 500) {
                                    $image_info = getimagesize($dir . "/" . $name);
                                    $width = $image_info[1];
                                    $height = $image_info[0];
                                    if ($sale->id_sources == 3) {
                                        $image = new ImageResize($dir . "/" . $name);
                                        if ($height >= $width) $image->crop($width, $height - 50);
                                        else $image->crop($width - 100, $height);
                                        $image->save($dir . "/" . $name);
                                    }
                                    if ($sale->id_sources == 1) {
                                        $image = new ImageResize($dir . "/" . $name);
                                        $image->crop($width, $height - 60);
                                        $image->save($dir . "/" . $name);
                                    }
                                    if ($sale->id_sources == 2) {
                                        $image = new ImageResize($dir . "/" . $name);
                                        $image->crop($width, $height - 60, ImageResize::CROPTOP);
                                        $image->save($dir . "/" . $name);
                                    }

                                } else {
                                    $sync = Synchronization::findOne($sale->id);
                                    //  $sync->parsed = 2;
                                    $sync->save();
                                    echo " ошибка загрузки";
                                    unlink($dir . "/" . $name);
                                }


                                /*echo "<br> открываем файл" . $dir . "/" . $name;
                                // my_var_dump(getimagesize($dir . "/" . $name));

                                $sourceImage = imagecreatetruecolor($width - 100, $height - 50);
                                imagecopyresized($sourceImage, imagecreatefromjpeg($dir . "/" . $name), 0, 0, 0, 0, $width - 100, $height - 50, $width, $height);
                                unlink($dir . "/" . $name);
                                imagejpeg($sourceImage, $dir . "/" . $name);*/
                                echo "<br> загружаем новый файл" . $dir . "/" . $name;
                            } else "<br> файл отсутствует";


                        }

                    }

                }
            } else info('НЕТ ФОТО');


            // скачиваем пачками по 10 шт.

            if ($n > 10) {
                info("СДЕЛАЛИ ЛИМИТ");
                break;
            }
        }


    }

    public function getMinPrice()
    {
        if ($this->white_list_id != '') {
            $min = Sale::find()->where(['in', 'id', explode(",", $this->white_list_id)])->min('price');
            return $min;
        }
    }

    public function getMaxPrice()
    {
        if ($this->white_list_id != '') {
            $min = Sale::find()->where(['in', 'id', explode(",", $this->white_list_id)])->max('price');
            return $min;
        }
    }

    public function getCount()
    {
        return count(explode(",", $this->white_list_id));

    }

    public function getLocalityFIlters($limit = 5, $type = 'public')
    {
        return SaleFilters::find()->where(['regions' => $this->regions])->limit($limit)->orderBy('name')->all();

    }

    public function getRelevantedIds_Web($type = SaleFilters::PUBLIC_TYPE, $limit = 10)
    {

        return SaleFilters::find()
            ->select('id,white_list_id,name,komment')
            ->where(['in', 'id', explode(",", $this->relevanted_ids)])
            ->andWhere(['type' => $type])
            ->limit($limit)
            ->all();

    }

    //  lists getters

    public function getWhite()
    {
        return Methods::convertToArrayWithBorders($this->white_list_id);
    }

    public function getBlack()
    {
        return Methods::convertToArrayWithBorders($this->black_list_id);
    }

    public function getSimilar_white()
    {
        return Methods::convertToArrayWithBorders($this->similar_white_list_id);
    }

    public function getSimilar_black()
    {
        return Methods::convertToArrayWithBorders($this->similar_black_list_id);
    }

    public function getProcessed()
    {
        return Methods::convertToArrayWithBorders($this->processed_ids);
    }

    public function getOnControls()
    {
        return $this->hasMany(SaleFiltersOnControl::className(), ['id_salefilter' => 'id']);
    }

    public function getControl()
    {

    }

    public function setControl($insert)
    {


    }


}