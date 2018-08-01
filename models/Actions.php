<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 05.03.2018
 * Time: 15:22
 */

namespace app\models;


use yii\rest\Action;
use app\components\Mdb;


use yii\helpers\Html;

class Actions
{

    // список коротких обозначений
    const SALE = 1;
    const SALEFILTER = 2;
    const SALELIST = 3;
    const SALEONCONTROL = 4;
    const SALESIMILAR = 5;
    const SYNC = 6;
    const AGENT = 7;
    const PARSING_CONTROL = 8;
    const PARSING_CONFIGURATION = 9;
    const PROXY = 15;
    const TAGS = 10;
    const ADDRESSES = 11;

    // Массовые Действия
    const TO_MANY_ADDRESSES = 'yter';
    const TO_MANY_SYNCRONISATIONS = 'wewr';

    // список Actions
    const  DELETE = 1;

    const DELETE_ICON = "<i class='fa fa-trash-o fa-fw fa-2x'></i>";

    // id списковых аттрибутов в каждой модели

    // SaleFilters
    const SALEFILTER_WHITE_LIST_ID = 1;
    const SALEFILTER_BLACK_LIST_ID = 2;
    const SALEFILTER_SIMILAR_WHITE_LIST_ID = 3;
    const SALEFILTER_SIMILAR_BLACK_LIST_ID = 4;
    const SALEFILTER_BLACK_ID_ADDRESSES = 5;
    const SALEFILTER_PROCESSED_IDS = 6;
    const SALEFILTER_RELEVANTED_IDS = 7;
    const DISACTIVE_ID_SOURCES = 8;
    const PROCESSED_IDS = 9;

    // SaleSimilar
    const SALESIMILAR_SIMILAR_IDS_ALL = 1;
    const SALESIMILAR_SIMILAR_IDS = 2;
    const SALESIMILAR_TAGS_IDS = 3;
    const SALESIMILAR_MODERATED = 4;
    const SALESIMILAR_MODERATION_ONCALL = 5;
    const SALESIMILAR_STATUS = 6;
    const SALESIMILAR_STATUS_SOLD = 7;

    // Sale
    const  SALE_DISACTIVE = 1;
    const  SALE_STATUS = 2;
    const  SALE_MODERATED = 4;
    const  SALE_MODERATION_ONCALL = 5;
    const  SALE_GEOCODATED = 6;
    const  SALE_GEOCODATION_ERROR = 7;
    const SALE_PROCESSED = 8;
    const SALE_PARSED = 9;

    // Parsing-Control
    const PARSING_CONTROL_DELETE = 1;


    // Agents
    const  AGENT_PERSON_TYPE = 1;
    const  AGENT_PERSON_TYPE_AGENT = 1;
    const  AGENT_PERSON_TYPE_HOUSEKEEPER = 0;


    // Parsing-Configuration
    const PARSING_CONFIGURATION_ACTIVE = 1;
    const PARSING_CONFIGURATION_ACTIVE_ACTIVATE = 1;
    const PARSING_CONFIGURATION_ACTIVE_DISACTIVATE = 0;
    const PARSING_CONFIGURATION_LAST_TIMESTAMP = 2;

    // PROXY
    const PROXY_STATUS = 1;
    const PROXY_STATUS_ACTIVE = 1;
    const PROXY_STATUS_DISACTIVE = 0;


    // TAGS
    const TAGS_SEARCHABLE = 1;
    const TAGS_SEARCHABLE_TRUE = 1;
    const TAGS_SEARCHABLE_FALSE = 0;

    // ADDRESSES
    const ADDRESSES_BALCON = 1;


    public static function getNameAttribute($id_model, $id_attr = 0)
    {

        $array = [
            Actions::SALEFILTER => [
                Actions::SALEFILTER_WHITE_LIST_ID => 'white_list_id',
                Actions::SALEFILTER_BLACK_LIST_ID => 'black_list_id',
                Actions::SALEFILTER_SIMILAR_WHITE_LIST_ID => 'similar_white_list_id',
                Actions::SALEFILTER_SIMILAR_BLACK_LIST_ID => 'similar_black_list_id',
                Actions::SALEFILTER_BLACK_ID_ADDRESSES => 'minus_id_addresses',
                Actions::SALEFILTER_RELEVANTED_IDS => 'relevanted_ids',
                Actions::SALEFILTER_RELEVANTED_IDS => 'relevanted_ids',
                Actions::DISACTIVE_ID_SOURCES => 'disactive_id_sources',
                Actions::SALEFILTER_PROCESSED_IDS => 'processed_ids',
            ],
            Actions::SALESIMILAR => [
                Actions::SALESIMILAR_SIMILAR_IDS_ALL => 'similar_ids_all',
                Actions::SALESIMILAR_SIMILAR_IDS => 'similar_ids',
                Actions::SALESIMILAR_TAGS_IDS => 'tags_ids',
                Actions::SALESIMILAR_MODERATED => 'moderated',
                Actions::SALESIMILAR_STATUS => 'status',
            ],

            Actions::SALE => [
                Actions::SALE_GEOCODATED => 'geocodated',
                Actions::SALESIMILAR_SIMILAR_IDS => 'similar_ids',
                Actions::SALESIMILAR_TAGS_IDS => 'tags_ids',
                Actions::SALESIMILAR_MODERATED => 'moderated',
                Actions::SALESIMILAR_STATUS => 'status',
                Actions::SALE_PROCESSED => 'processed',
            ],
            Actions::SYNC => [
                Actions::SALE_GEOCODATED => 'geocodated',
                Actions::SALESIMILAR_SIMILAR_IDS => 'similar_ids',
                Actions::SALESIMILAR_TAGS_IDS => 'tags_ids',
                Actions::SALESIMILAR_MODERATED => 'moderated',
                Actions::SALESIMILAR_STATUS => 'status',
                Actions::SALE_PROCESSED => 'processed',
                Actions::SALE_PARSED => 'parsed',
            ],
            Actions::PARSING_CONFIGURATION => [
                Actions::PARSING_CONFIGURATION_ACTIVE => 'active',
                Actions::PARSING_CONFIGURATION_LAST_TIMESTAMP => 'last_timestamp',

            ],
            Actions::AGENT => [
                Actions::AGENT_PERSON_TYPE => 'person_type',

            ],
            Actions::PROXY => [
                Actions::PROXY_STATUS => 'status',

            ],
            Actions::TAGS => [
                Actions::TAGS_SEARCHABLE => 'searchable',

            ],
            Actions::ADDRESSES => [
                Actions::ADDRESSES_BALCON => 'balcon',

            ],


        ];
        if ($id_attr == 0) return $array[$id_model];

        return $array[$id_model][$id_attr];
    }


    public static function getIcons($id_model, $id_attr = 0, $is_active = false)
    {
        if ($is_active) {
            $color = "green-text";
            $title = "Показывать";
        } else {
            $color = "";
            $title = "Не показывать";
        }

        $array = [

            Actions::SALEFILTER => [
                Actions::SALEFILTER_WHITE_LIST_ID => Mdb::Fa("star-o fa-fw " . $color . " fa-2x",
                    ['title' => "Отложить вариант "]),
                Actions::SALEFILTER_BLACK_LIST_ID => Mdb::Fa("eye-slash fa-fw " . $color . " fa-2x", ['title' => "Непоказывать вариант "]),
                Actions::SALEFILTER_SIMILAR_WHITE_LIST_ID => Mdb::Fa("plus-square-o fa-fw " . $color . " fa-2x", ['title' => "Отложить похожие варианты "]),
                Actions::SALEFILTER_SIMILAR_BLACK_LIST_ID => Mdb::Fa("minus-square-o fa-fw " . $color . " fa-2x",
                    ['title' => " непоказывать похожие варианты "]),
                Actions::SALEFILTER_BLACK_ID_ADDRESSES => "<span class=\"fa-stack fa-lg fa-fw " . $color . "\" title=\"" . $title . " данный адрес\">
<i class=\"fa fa-map-marker fa-stack-1x\" aria-hidden=\"true\"></i>
<i class=\"fa fa-ban fa-stack-2x text-danger\"></i>
</span>",
                Actions::SALEFILTER_PROCESSED_IDS => Mdb::Fa("check fa-fw " . $color . " fa-2x"),
                // Actions::SALEFILTER_WHITE_ID_ADDRESSES => 'plus_id_addresses',
                Actions::SALEFILTER_RELEVANTED_IDS => 'relevanted_ids',
                Actions::DISACTIVE_ID_SOURCES => 'disactive_id_sources',
                Actions::PROCESSED_IDS => 'processed_ids',
            ],

            Actions::SALESIMILAR => [
                Actions::SALESIMILAR_SIMILAR_IDS_ALL => 'similar_ids_all',
                Actions::SALESIMILAR_SIMILAR_IDS => 'similar_ids',
                Actions::SALESIMILAR_TAGS_IDS => 'tags_ids',
                Actions::SALESIMILAR_MODERATED => Mdb::Fa("check fa-fw " . $color . " fa-2x", ['title' => "Промодерировать "]),
                Actions::SALESIMILAR_MODERATION_ONCALL => Mdb::Fa("phone fa-fw " . $color . " fa-2x", ['title' => "Модерация по звонку"]),
                Actions::SALESIMILAR_STATUS => 'status',
                Actions::SALESIMILAR_STATUS_SOLD => "<i class=\"fa fa-trash fa-fw fa-2x\" aria-hidden=\"true\" title=\"Продано\"></i>",
            ],
            Actions::PARSING_CONFIGURATION => [
                Actions::PARSING_CONFIGURATION_ACTIVE_ACTIVATE => Mdb::Fa("play fa-fw " . $color . " fa-2x", ['title' => "Запустить "]),
                Actions::PARSING_CONFIGURATION_ACTIVE_DISACTIVATE => Mdb::Fa("pause fa-fw " . $color . " fa-2x", ['title' => "Пауза"]),
                Actions::PARSING_CONFIGURATION_LAST_TIMESTAMP => Mdb::Fa("step-backward fa-fw " . $color . " fa-2x", ['title' => "ЗАНОВО"]),

            ],
            Actions::AGENT => [
                Actions::AGENT_PERSON_TYPE_AGENT => Mdb::Fa("user-secret fa-fw  fa-2x" . $color . " fa-2x", ['title' => "Агент "]),
                Actions::AGENT_PERSON_TYPE_HOUSEKEEPER => Mdb::Fa("address-book fa-fw " . $color . " fa-2x", ['title' => "Собственник "]),


            ],
            Actions::SALE => [
                Actions::SALE_MODERATED => Mdb::Fa("check  fa-fw" . $color . " fa-2x", ['title' => "Промодерировать "]),
                Actions::SALE_MODERATION_ONCALL => Mdb::Fa("phone fa-fw" . $color . " fa-2x", ['title' => "Модерация по звонку"]),
                Actions::SALE_GEOCODATION_ERROR => "<span class=\"fa-stack fa-lg fa-fw\">
                                                        <i class=\"fa fa-map fa-stack-1x\"></i>
                                                        <i class=\"fa fa-ban fa-stack-2x text-danger\" title='Не правильно указан адрес'></i>
                                                    </span>",
            ],
            Actions::TAGS => [
                Actions::TAGS_SEARCHABLE_FALSE => ICON_NOSEARCH,
                Actions::TAGS_SEARCHABLE_TRUE => ICON_SEARCH,
            ]


        ];

        return $array[$id_model][$id_attr];
    }


    public static function Remove($id_parent, $id_model, $id_attr, $id)
    {
        $model = Actions::getModel($id_model, $id_parent);
        if ($model) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $attrWas = $model[$attrname];
            $model[$attrname] = preg_replace("/," . $id . ",/", ",", $attrWas);

            $return = " Успешно изменили " . $id_model . ":" . $id_attr . " c " . $attrWas . " на '" . $model[$attrname] . "'";
            $model->save();
//            echo $return;

        } else echo "<br>" . span(" PARENT IS NOT EXISTS", 'danger');

        return $return;
    }

    public
    static function Add($id_parent, $id_model, $id_attr, $id)
    {
        $model = Actions::getModel($id_model, $id_parent);
        if ($model) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $attrWas = $model[$attrname];
            $model[$attrname] = $attrWas . "" . $id . ",";
            $return = " Успешно изменили " . $id_model . ":" . $id_attr . " c " . $attrWas . " на '" . $model[$attrname] . "'";

            if (!$model->validate()) info($model->getErrors());
            if (!$model->save(false)) info($model->getErrors());

        } else echo "<br>" . span(" PARENT IS NOT EXISTS", 'danger');
        echo $return;
        return $return;
    }

    public
    static function Toggle($id_parent, $id_model, $id_attr, $id)
    {
        $model = Actions::getModel($id_model, $id_parent);
        if ($model) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $attrWas = $model[$attrname];
            if ($attrWas) {
                $model[$attrname] = preg_replace("/," . $id . ",/", ",", $attrWas);
                if ($model[$attrname] == ',') $model[$attrname] = '';
                if ($model[$attrname] == $attrWas) $model[$attrname] = $attrWas . "" . $id . ",";

            } else $model[$attrname] = "," . $id . ",";
            $return = " Успешно изменили " . $id_model . ":" . $id_attr . " c " . $attrWas . " на '" . $model[$attrname] . "'";
            //  if (!$model->validate()) my_var_dump($model->getErrors());
            if (!$model->save(false)) my_var_dump($model->getErrors());
        } else echo "<br>" . span(" PARENT IS NOT EXISTS", 'danger');


        return $return;


    }

    public
    static function ChangeStatus($id_parent, $id_model, $id_attr, $id_status)
    {
        if ($id_parent === Actions::TO_MANY_ADDRESSES) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $session = \Yii::$app->session;
            $id_addresses = $session->get('addresses');

            if ($id_addresses) $count = Addresses::updateAll([$attrname => $id_status], ['in', 'id', $id_addresses]);
            return $count;

        }
        if ($id_parent === Actions::TO_MANY_SYNCRONISATIONS) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $session = \Yii::$app->session;
            $id_synchronisations = $session->get('synchronisations');
            if ($id_synchronisations) $count = Synchronization::updateAll([$attrname => $id_status], ['in', 'id', $id_synchronisations]);
            return $count;
        }

        $model = Actions::getModel($id_model, $id_parent);
        if ($model) {
            $attrname = Actions::getNameAttribute($id_model, $id_attr);
            $attrWas = $model[$attrname];
            $model->$attrname = $id_status;
            $return = " Успешно изменили " . $id_model . ":" . $id_attr . " c " . $attrWas . " на " . $model[$attrname];
            my_var_dump($model);
            if (!$model->save(false)) my_var_dump($model->errors);
        } else {

            echo "<br>" . span(" PARENT IS NOT EXISTS", 'danger');
        }


        return $return;


    }


    /*
     * метод который ищет модель соглавно ее $id_model, $id_parent
     * */
    protected
    static function getModel($id_model, $id_parent)
    {
        switch ($id_model) {
            case Actions::SALEFILTER :
                return SaleFilters::findOne($id_parent);
            case Actions::SALE :
                return Sale::findOne($id_parent);

            case Actions::SALESIMILAR :
                return SaleSimilar::findOne($id_parent);

            case Actions::SALELIST :
                return SaleLists::findOne($id_parent);

            case Actions::SYNC :
                return Synchronization::findOne($id_parent);

            case Actions::AGENT :
                return Agents::findOne($id_parent);

            case Actions::PARSING_CONTROL :
                return ControlParsing::findOne($id_parent);

            case Actions::PARSING_CONFIGURATION :
                return ParsingConfiguration::findOne($id_parent);

            case Actions::PROXY :
                return Proxy::findOne($id_parent);

            case Actions::TAGS :
                return Tags::findOne($id_parent);

            default:
                echo "<br>" . span(" MODEL IS NOT EXISTS", 'danger');


        }
    }

    // методы для рендеринга кнопки для ajax-действий

    public static function renderChangeStatus($id_parent, $id_model, $id_attr, $id_status, $text, $options = [])
    {
        return Html::a($text, false, [
            'data' => ['id_parent' => $id_parent, 'id_model' => $id_model, 'id_attr' => $id_attr, 'id_status' => $id_status],
            'class' => "change-statuses " . $options['class'],
            // 'style' => "margin-left: 4px;margin-right: 4px;"
        ]);
    }

    public static function renderToggleLists($id_parent, $id_model, $id_attr, $id, $text, $options = [])
    {
        return Html::a($text, false, [
            'data' => ['id_parent' => $id_parent, 'id_model' => $id_model, 'id_attr' => $id_attr, 'id' => $id],
            'class' => "toggle-action-lists " . $options['class']
        ]);

    }

    public static function renderAction($id_parent, $id_model, $id_action, $text, $options = [])
    {
        return Html::a($text, false, [
            'data' => ['id_parent' => $id_parent, 'id_model' => $id_model, 'id_action' => $id_action],
            'class' => "action " . $options['class'],
            // 'style' => "margin-left: 4px;margin-right: 4px;"
        ]);

    }

    public
    static function Action($id_parent, $id_model, $id_action)
    {
        $model = Actions::getModel($id_model, $id_parent);
        if ($model) {
            switch ($id_action) {
                case Actions::DELETE :
                    $model->delete();
                    $return = " Успешно удалили " . $id_model . ": id=" . $id_parent;

            }

        } else echo "<br>" . span(" PARENT IS NOT EXISTS", 'danger');

        return $return;

    }


}