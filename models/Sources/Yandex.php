<?php

namespace app\models\sources;

use app\models\Cian;
use app\models\AgentPro;
use app\models\Selectors;
use yii\helpers\ArrayHelper;

/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 02.06.2018
 * Time: 8:24
 */
class Yandex extends Cian
{

    public static function loadTableClasses($pageSource)

    {
        /* @var $container_selector Selectors */
        /* @var $container_in_selector Selectors */

        $containers_selectors = Selectors::find()
            ->where(['id_sources' => 2])
            ->andWhere(['type' => Selectors::TYPE_TABLE_CONTAINER])
            ->indexBy('alias')
            //  ->asArray()
            ->all();
        my_var_dump(ArrayHelper::map($containers_selectors, 'alias', 'pattern'));
        if ($containers_selectors) {
            foreach ($containers_selectors as $container_selector) {
                if ($container_selector->check($pageSource)) {
                    $container_in_selectors = Selectors::find()->where(['id_parent' => $container_selector->id])->all();
                    if ($container_in_selectors) {
                        foreach ($container_in_selectors as $container_in_selector) {
                            $containerSource = \phpQuery::newDocument($pageSource)->find(".".$container_selector->selector)->eq(0)->html();

                            $container_in_selector->check($containerSource);

                        }
                    }
                }


            }
        }


    }

    // Данный метод проверяет и загружает в статические свойства вгенерированные классы CIAN
    public static function loadPageClasses($pageSource)
    {
        self::page_title_div_class($pageSource);
        self::page_address_div_class($pageSource);
        self::page_address_item_div_class($pageSource);
        self::page_photorama_div_class($pageSource);
        self::page_link_coords_div_class($pageSource);
        self::page_person_title_div_class($pageSource);
        self::page_phone_div_class($pageSource);
        self::page_description_div_class($pageSource);
        self::page_info_block_div_class($pageSource);
        self::page_info_block2_div_class($pageSource);

    }

    public static function throwError($id_error)
    {
        AgentPro::stop($id_error);
        info(AgentPro::ErrorLogs()[$id_error], 'danger');
        if (!self::DEBUG_MODE) die();
    }

    public static function check($selector, $pageSource)
    {
        /* @var $selector Selectors */
        $pattern = "/" . preg_quote($selector->pattern) . "/isU";
        if (preg_match($pattern, $pageSource, $output_array)) {
            my_var_dump($output_array);
            return self::$pageTitleDivClass = $output_array[1];
            //  return $output_array[1];
        } else {
            self::throwError($selector->id_error);
        }


    }

}