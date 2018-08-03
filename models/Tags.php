<?php

namespace app\models;

use app\components\Mdb;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * This is the model class for table "tags".
 *
 * @property integer $id
 * @property string $name
 * @property integer $parent
 * @property string $locality
 * @property integer $type
 * @property integer $a_type
 * @property string $color
 * @property string $komment
 * @property string $patterns
 * @property string $minus_patterns
 */
class Tags extends \yii\db\ActiveRecord
{
    const ACTIVE_TAG_CLASS = "border-tag z-depth-5";
    const ACTIVE_TAG_CLASS_EXIT = "animated slideOutLeft";
    const ACTIVE_TAG_CLASS_APPEARENCE = "animated pulse infinite z-depth-5";
    //  const TYPES = 'building,plan,object,locality,condition,deal';
    const TYPES = 'building,plan,locality,condition,deal';
    const TYPES_ARRAY = [
        '1' => 'Район',
        '2' => 'Дом',
        '3' => 'План-ка',
        '5' => 'Сост.',
        '6' => 'Сделка'
    ];
    const PUBLIC_TYPES_ARRAY = [
        '1' => 'Район',
        '2' => 'Дом',
        '3' => 'Планировка',
        '4' => 'Дополнительно',
        '5' => 'Состояние',
        '6' => 'Детали сделки'
    ];
    const PARENTS_ARRAY = [
        0 => 'Объект',
        1 => 'Клиент',
        2 => 'Список',
        3 => 'Агент',
        4 => 'Фильтр'

    ];
    const PUBLIC_ARRAY = [
        '0' => 'Публичный',
        '1' => 'Непубличный'
    ];
    const COLORS = [
        'default' => 'обычный',
        'danger' => 'Отрицательное',
        'success' => 'Положительное',
        'black' => 'Существенный минус',
    ];
    const TYPES_BALCONY = 1; // БАЛКОН
    const TYPES_WC = 2; // С/у
    const TYPES_DEVELOPER = 3; // КТО ЗАСТРОЙЩИк
    const TYPES_IS_BUILT = 4; // ДОМ СДАН ИЛИ НЕТ
    const TYPES_CONDITION = 5; // ОБЩЕЕ СОСТОЯНИЕ
    const TYPES_HOUSE_PLAN = 6; // ТИП ПЛАНИРОВКИ ДОМА
    const TYPES_DEAL = 7; // ФОРМА РАСЧЕТА
    const TYPES_OWNER = 8; //   СОБСТВЕННИК
    const TYPES_ROOMS_PLAN = 9; //   ПЛАНИРОВКА КОМНАТ
    const TYPES_NEAR_LOCATION = 10; //   ПРИГОРОД
    const TYPES_CLIENTS = 11; //   ТИП КЛИЕНТА
    const TYPES_FLOORS = 12; //   ПЛАНИРОВКА КОМНАТ
    const TYPES_HEATING = 13; //   ТИП ОТОПЛЕНИЯ
    const TYPES_SELL = 14; //   ТИП ПРОДАЖИ
    const TYPES_WATER = 15; //   ТИП ВОДЫ

    public static function A_TYPES()
    {
        return [
            Tags::TYPES_BALCONY => "БАЛКОН",
            Tags::TYPES_WC => 'С/У',
            Tags::TYPES_DEVELOPER => 'ЗАСТРОЙЩИК',
            //  Tags::TYPES_IS_BUILT => 'ДОМ СДАН',
            Tags::TYPES_CONDITION => 'СОСТОЯНИЕ',
            Tags::TYPES_HOUSE_PLAN => 'ПЛАНИРОВКА ДОМА',
            Tags::TYPES_DEAL => 'ТИП СДЕЛКИ'];
    }

    const A_TYPES = [
        Tags::TYPES_BALCONY => "БАЛКОН",
        Tags::TYPES_WC => 'С/У',
        Tags::TYPES_DEVELOPER => 'ЗАСТРОЙЩИК',
        Tags::TYPES_IS_BUILT => 'ГОТОВНОСТЬ ДОМА',
        Tags::TYPES_CONDITION => 'СОСТОЯНИЕ',
        Tags::TYPES_HOUSE_PLAN => 'ПЛАНИРОВКА ДОМА',
        Tags::TYPES_ROOMS_PLAN => 'ПЛАНИРОВКА КВАРТИРЫ',
        Tags::TYPES_DEAL => 'ФОРМА РАСЧЕТА',
        Tags::TYPES_OWNER => 'СОБСТВЕННИК',
        Tags::TYPES_NEAR_LOCATION => 'ПРИГОРОД',
        Tags::TYPES_CLIENTS => 'ТИП КЛИЕНТА',
        Tags::TYPES_HEATING => 'ТИП ОТОПЛЕНИЯ',
        Tags::TYPES_SELL => 'ТИП ПРОДАЖИ',
        Tags::TYPES_WATER => 'ВОДОСНАБЖЕНИЕ',

    ];


    public static function Additional_TYPES($type = 0)
    {
        $array = [
            2 => [3, 4],
            3 => [1, 6],
            // 4 => [1],
            5 => [2],
            6 => [7]
        ];
        if ($type) return $array[$type];
        else return $array;

    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tags';
    }

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    public function beforeSave($insert)
    {
        Tags::updateAllCache();
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function beforeValidate()
    {
        $booleans = ['searchable'];
        foreach ($booleans as $property) {
           // info("PRELOAD PROPERTY ".$property." value =".$this[$property]);
            if (!$this[$property])  $this[$property] = 0; else $this[$property] = 1;
        }


        return parent::beforeValidate(); // TODO: Change the autogenerated stub
    }

    public function load($data, $formName = null)
    {
       $return =  parent::load($data, $formName);

        $booleans = ['searchable'];
        foreach ($booleans as $property) {
          //  info("PRELOAD PROPERTY ".$property." value =".$this[$property]);
            if (!$this[$property]) $this[$property] = 0; else $this[$property] = 1;
        }

        return $return;

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'color'], 'required'],
            [['parent', 'type', 'a_type','id_parent', 'publish', 'searchable','global_parent'], 'integer'],
            [['name', 'komment'], 'string', 'max' => 256],
            [['locality'], 'string', 'max' => 30],
            [['color'], 'string', 'max' => 15],
            [['patterns', 'minus_patterns'], 'string', 'max' => 256],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'global_parent' => 'global_parent',
            'id_parent' => 'Tag parent',
            'locality' => 'Локальность',
            'type' => 'Тип',
            'a_type' => 'Тип2',
            'color' => 'Color',
            'komment' => 'Komment',
            'patterns' => 'Шаблон автозагрузки',
        ];
    }


    public static function setToMany($id_tag,$to) {
        switch ($to) {
            case 'setToAllAddresses': {
                $session = \Yii::$app->session;
                $id_addresses = $session->get('addresses');
              //  my_var_dump($id_addresses);
                $count = count($id_addresses);
             if (!$count) Mdb::Alert(" NOTHING TO CHANGE",DANGER);

                if ($addresses = Addresses::find()->where(['in', 'id', $id_addresses])->all()) {
                    foreach ($addresses as $address) {
                        echo "<br>" . $address->tags_id;
                        if (strpos("START" . $address->tags_id, "," . $id_tag . ",")) {
                          //  info(" TAG " . $id_tag . " IS IN SALE TAGS " . $address->tags_id, SUCCESS);
                            $infoCounter++;

                        } else {
                         //   info(" TAG " . $id_tag . " IS NOT IN SALE TAGS " . $address->tags_id, DANGER);
                            $address->tags_id = Methods::addToList($id_tag,$address->tags_id);
                            $successCounter++;

                        }
                      //  echo "<br>" . $address->tags_id;
                        $address->save();
                    }
                    Mdb::Alert(" NEW ".$successCounter." OLD ".$infoCounter." FROM ".$count ." ADDRESSES",SUCCESS);



                }
                break;
            }
        }
    }

    public function init()
    {
        $this->a_type = 0;
        parent::init(); // TODO: Change the autogenerated stub
    }

    public static function getGroupedTags($type = '',$searchable = false)
    {

        $session = Yii::$app->session;
        //echo $session->get('city_module');
        switch ($type) {
            case 'address':
                {
                    if (empty(Yii::$app->cache->get('tags_locality'))) {
                        Yii::$app->cache->set('tags_locality', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andWhere(['global_parent' => 0])
                            ->andwhere(['type' => 1])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    if (empty(Yii::$app->cache->get('tags_building'))) {
                        Yii::$app->cache->set('tags_building', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andWhere(['global_parent' => 0])
                            ->andwhere(['type' => 2])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }

                    $tags_locality = Yii::$app->cache->get('tags_locality');
                    $tags_building = Yii::$app->cache->get('tags_building');


                    $array = [
                        '0' => ['name' => 'Геотеги','icon' => 'map.png','tags' => $tags_locality],
                        '1' => ['name' => 'Здание', 'icon' => 'building.png','tags' => $tags_building],
                    ];
                    break;
                }
            case 'sale':
                {
                    if (empty(Yii::$app->cache->get('tags_plan'))) {
                        Yii::$app->cache->set('tags_plan', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andWhere(['global_parent' => 0])
                            ->andwhere(['type' => 3])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_plan = Yii::$app->cache->get('tags_plan');

//                    if (empty(Yii::$app->cache->get('tags_object'))) {
//                        Yii::$app->cache->set('tags_object', \app\models\Tags::find()
//                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
//                            ->andWhere(['global_parent' => 0])
//                            ->andwhere(['type' => 4])
//                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
//                            ->all());
//                    }
//                    $tags_object = Yii::$app->cache->get('tags_object');

                    if (empty(Yii::$app->cache->get('tags_condition'))) {
                        Yii::$app->cache->set('tags_condition', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andWhere(['global_parent' => 0])
                            ->andwhere(['type' => 5])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_condition = Yii::$app->cache->get('tags_condition');


                    if (empty(Yii::$app->cache->get('tags_deal'))) {
                        Yii::$app->cache->set('tags_deal', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andWhere(['global_parent' => 0])
                            ->andwhere(['type' => 5])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_deal = Yii::$app->cache->get('tags_deal');
                    $array = [

                        '2' => ['name' => 'Планировка', 'icon' => 'scheme.png', 'tags' => $tags_plan],
                        //  '3' => ['name' => 'Объект', 'tags' => $tags_object],
                        '4' => ['name' => 'Состояние', 'icon' => 'repair.png', 'tags' => $tags_condition],
                        '5' => ['name' => 'Сделка', 'icon' => 'deal.png', 'tags' => $tags_deal]

                    ];
                }
                break;
            default :
                {
                    if (empty(Yii::$app->cache->get('tags_locality'))) {
                        Yii::$app->cache->set('tags_locality', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andwhere(['type' => 1])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_locality = Yii::$app->cache->get('tags_locality');
                    if (empty(Yii::$app->cache->get('tags_building'))) {
                        Yii::$app->cache->set('tags_building', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andwhere(['type' => 2])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }


                    $tags_building = Yii::$app->cache->get('tags_building');

                    if (empty(Yii::$app->cache->get('tags_plan'))) {
                        Yii::$app->cache->set('tags_plan', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andwhere(['type' => 3])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_plan = Yii::$app->cache->get('tags_plan');

//                    if (empty(Yii::$app->cache->get('tags_object'))) {
//                        Yii::$app->cache->set('tags_object', \app\models\Tags::find()
//                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
//                            ->andwhere(['type' => 4])
//                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
//                            ->all());
//                    }
//                    $tags_object = Yii::$app->cache->get('tags_object');

                    if (empty(Yii::$app->cache->get('tags_condition'))) {
                        Yii::$app->cache->set('tags_condition', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andwhere(['type' => 5])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_condition = Yii::$app->cache->get('tags_condition');


                    if (empty(Yii::$app->cache->get('tags_deal'))) {
                        Yii::$app->cache->set('tags_deal', \app\models\Tags::find()
                            ->where(['in', 'locality', ['default', $session->get('city_module')]])
                            ->andwhere(['type' => 6])
                            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
                            ->all());
                    }
                    $tags_deal = Yii::$app->cache->get('tags_deal');

                    if ($searchable) {
                        $tags_locality = array_filter($tags_locality,function ($tag) {
                           return $tag['searchable'];
                        }) ;
                        $tags_building = array_filter($tags_building,function ($tag) {
                           return $tag['searchable'];
                        }) ;
                        $tags_plan = array_filter($tags_plan,function ($tag) {
                           return $tag['searchable'];
                        }) ;
                        $tags_condition = array_filter($tags_condition,function ($tag) {
                           return $tag['searchable'];
                        }) ;
                        $tags_deal = array_filter($tags_deal,function ($tag) {
                           return $tag['searchable'];
                        }) ;

                    }

                    $array = [
                        '0' => ['name' => 'Геотеги', 'icon' => 'map.png', 'tags' => $tags_locality],
                        '1' => ['name' => 'Здание', 'icon' => 'building.png', 'tags' => $tags_building],
                        '2' => ['name' => 'Планировка', 'icon' => 'scheme.png', 'tags' => $tags_plan],
                        //  '3' => ['name' => 'Объект', 'tags' => $tags_object],
                        '4' => ['name' => 'Состояние', 'icon' => 'repair.png', 'tags' => $tags_condition],
                        '5' => ['name' => 'Сделка', 'icon' => 'deal.png', 'tags' => $tags_deal]
                    ];
                    break;
                }

        }

        return $array;
    }

    public static function render($tags, $pre = '#')
    {
        if (!empty($tags)) {
            if (empty(Yii::$app->cache->get('tags'))) {
                Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());
            }
            $all_tags = Yii::$app->cache->get('tags');
            $tags_render = '';
            foreach ($tags as $tag) {

                $tags_render .= "<span class=\"badge badge-" . $all_tags[$tag]['color'] . "\">" . $pre . "" . $all_tags[$tag]['name'] . "</span> ";
                //  $tags_render .=  $all_tags[$tag]['name'];

            }
            $tags_render .= "<br>";
        }
        return $tags_render;

    }

    public static function updateAllCache()
    {
        Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());
        Yii::$app->cache->set('patterned_tags', Tags::find()->where(['<>', 'patterns', ''])->all());

        $session = Yii::$app->session;
        Yii::$app->cache->set('tags_locality', \app\models\Tags::find()
            ->where(['in', 'locality', ['default', $session->get('city_module')]])
            ->andWhere(['global_parent' => 0])
            ->andwhere(['type' => 1])
            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
            ->all());


        Yii::$app->cache->set('tags_building', \app\models\Tags::find()
            ->where(['in', 'locality', ['default', $session->get('city_module')]])
            ->andWhere(['global_parent' => 0])
            ->andwhere(['type' => 2])
            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
            ->all());

        Yii::$app->cache->set('tags_plan', \app\models\Tags::find()
            ->where(['in', 'locality', ['default', $session->get('city_module')]])
            ->andWhere(['global_parent' => 0])
            ->andwhere(['type' => 3])
            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
            ->all());

//        Yii::$app->cache->set('tags_object', \app\models\Tags::find()
//            ->where(['in', 'locality', ['default', $session->get('city_module')]])
//            ->andWhere(['global_parent' => 0])
//            ->andwhere(['type' => 4])
//            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
//            ->all());

        Yii::$app->cache->set('tags_condition', \app\models\Tags::find()
            ->where(['in', 'locality', ['default', $session->get('city_module')]])
            ->andWhere(['global_parent' => 0])
            ->andwhere(['type' => 5])
            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
            ->all());

        Yii::$app->cache->set('tags_deal', \app\models\Tags::find()
            ->where(['in', 'locality', ['default', $session->get('city_module')]])
            ->andWhere(['global_parent' => 0])
            ->andwhere(['type' => 6])
            ->orderBy(['type' => SORT_ASC, 'a_type' => SORT_DESC])
            ->all());

        Yii::$app->cache->set('tags', Tags::find()->indexBy('id')->asArray()->all());

    }

    public static function convertToString(array $arr)
    {
        if (!empty($arr)) return "," . implode(",", $arr) . ",";
        else return '';
    }
    public function getParent() {
        return $this->hasOne(Tags::className(),['id_parent' => 'id']);
    }
    public function getChilds() {
        return $this->hasMany(Tags::className(),['id' => 'id_parent']);
    }

    public static function mapParents() {
        return ArrayHelper::map(Tags::find()->all(),'id','name');
    }



    public static function convertToArray($string)
    {
        if ($string) {
            $response = explode(",", preg_replace("/^,|,$/", "", $string));
            if (is_array($response)) return $response;
            elseif (is_int($response)) return [$response];
        } else return [];
    }

    public static function renderActiveTag($parent_id, $tag, array $realtags = [], $type, $a_type = '')
    {
        if ($a_type) $a_type = "a_type" . $a_type;
        if (in_array($tag->id, $realtags)) $class = "animated pulse infinite z-depth-5";
        $a = Html::a(
            Html::tag('span', "<p class='h5 - responsive' style='margin - bottom: 0px'>" . $tag->name . "</p>",
                [ // span
                    'class' => "tag_" . $type . "_" . $parent_id . "_" . $tag->id . " badge badge-" . $tag->color . " " . $class . " check-tags " . $a_type . "",
                    //'id' => "tag_" . $type . "_" . $parent_id . "_" . $tag->id
                ]), false,
            [ // a
                'class' => "tags-action-button ",
                'data' => [
                    'parent_id' => $parent_id,
                    'tag_id' => $tag->id,
                    'type' => $type,
                    'a_type' => $a_type
                ]
            ]);
        return $a;
    }

    public static function renderActiveTagNewer($parent_id, $tag, array $realtags = [], $type, $a_type = '')
    {
        if ($a_type) $a_type = "a_type" . $a_type;
        if (in_array($tag->id, $realtags)) $class = self::ACTIVE_TAG_CLASS;
        $a = Html::a(
            Html::tag('span', $tag->name,
                [ // span
                    'class' => "tag_" . $type . "_" . $parent_id . "_" . $tag->id . " badge badge-" . $tag->color . " " . $class . " check-tags " . $a_type . "",
                    //'id' => "tag_" . $type . "_" . $parent_id . "_" . $tag->id
                ]), false,
            [ // a
                'class' => "tags-action-button ",
                'data' => [
                    'parent_id' => $parent_id,
                    'tag_id' => $tag->id,
                    'type' => $type,
                    'a_type' => $a_type
                ]
            ]);
        return $a;
    }

    public function beforeDelete()
    {
        Tags::deleteTagsLink($this->id);
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /*
     * метод для удаления тегов из всех ресурсов где он есть если мы его удаляем
     * */

    public static function deleteTagsLink($id_tag)
    {

        $model_delete_from = [Sale::className(), SaleSimilar::className(), SaleFilters::className(), Addresses::className()];
        //  $model_delete_from = [Sale::className()];
        foreach ($model_delete_from as $model) {
            $query = $model::find()->where(['like', 'tags_id', "," . $id_tag . ","]);
            $count = $query->count();
            if ($count) {
                info(" LOST =" . $count . " id tags " . $id_tag . " in " . $model, 'alert');
                $missed_tags = $query->limit(50)->all();
                if ($missed_tags) {
                    foreach ($missed_tags as $missed_tag) {
                        info($missed_tag->tags_id);
                        $missed_tag->tags_id = preg_replace("/," . $id_tag . ",/", ",", $missed_tag->tags_id);
                        if ($missed_tag->tags_id == ',') $missed_tag->tags_id = '';
                        info($missed_tag->tags_id, 'success');
                        $missed_tag->save();

                    }
                } // else info(" noothing to find");

            }

        }


    }


}
