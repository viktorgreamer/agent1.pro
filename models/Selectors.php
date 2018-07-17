<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "app_selectors".
 *
 * @property integer $id
 * @property integer $id_sources
 * @property integer $type
 * @property string $pattern
 * @property integer $count
 * @property integer $id_parent
 * @property integer $id_error
 */
class Selectors extends \yii\db\ActiveRecord
{

    const TYPE_TABLE = 1;
    const TYPE_TABLE_CONTAINER = 2;
    const TYPE_DETAILED = 3;
    const TYPE_STAT = 4;

    const  SELECTOR_COUNT_1 = 1;
    const  SELECTOR_COUNT_MORE_1 = 2;
    const  SELECTOR_CIAN_10 = 3;
    const  SELECTOR_CIAN_11 = 4;
    const  SELECTOR_YANDEX_00 = 5;


    public static function getCounts()
    {
        return [
            self::SELECTOR_COUNT_1 => "1",
            self::SELECTOR_COUNT_MORE_1 => "MORE 1",
            self::SELECTOR_CIAN_10 => "CIAN_10",
            self::SELECTOR_CIAN_11 => "CIAN_11",
            self::SELECTOR_YANDEX_00 => "YANDEX_00"
        ];
    }

    public static function getParents()
    {
        return ArrayHelper::map(Selectors::find()->where(['type' => self::TYPE_TABLE_CONTAINER])->all(), 'id', 'alias');
    }

    public static function getTypes()
    {
        return [
            self::TYPE_TABLE => "TABLE",
            self::TYPE_TABLE_CONTAINER => "TABLE CONTAINER",
            self::TYPE_DETAILED => "DETAILED",
            self::TYPE_STAT => "STAT"
        ];
    }


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'app_selectors';
    }

    public static function loadTableClasses($pageSource, $id_source)

    {
        /* @var $container_selector Selectors */
        /* @var $container_in_selector Selectors */

        $containers_selectors = Selectors::find()
            ->where(['id_sources' => $id_source])
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
                            $containerSource = \phpQuery::newDocument($pageSource)->find("." . $container_selector->selector)->eq(0)->html();
                            $container_in_selector->check($containerSource);

                        }
                    }
                }


            }
            Selectors::setSelectors(Selectors::TYPE_TABLE, $id_source);
        }


    }

    public static function loadPageClasses($pageSource, $id_source)

    {
        /* @var $selector Selectors */

        $selectors = Selectors::find()
            // ->select('alias,pattern')
            ->where(['id_sources' => $id_source])
            ->andWhere(['type' => Selectors::TYPE_DETAILED])
            //->indexBy('alias')
            //  ->asArray()
            ->all();

        // my_var_dump(ArrayHelper::map($selectors, 'alias', 'pattern'));
        if ($selectors) {
            foreach ($selectors as $selector) {

                // info("pattern = ".htmlspecialchars($selector->pattern));
                $selector->check($pageSource);
            }

            Selectors::setSelectors(Selectors::TYPE_DETAILED, $id_source);
        }


    }

    public static function loadStatClasses($pageSource, $id_source)

    {
        /* @var $selector Selectors */

        $selectors = Selectors::find()
            // ->select('alias,pattern')
            ->where(['id_sources' => $id_source])
            ->andWhere(['type' => Selectors::TYPE_STAT])
            //->indexBy('alias')
            //  ->asArray()
            ->all();

        // my_var_dump(ArrayHelper::map($selectors, 'alias', 'pattern'));
        if ($selectors) {
            foreach ($selectors as $selector) {

                // info("pattern = ".htmlspecialchars($selector->pattern));
                $selector->check($pageSource);
            }

            Selectors::setSelectors(Selectors::TYPE_STAT, $id_source);
        }


    }





    public function check($pageSource)
    {

        $pattern = "/" . $this->pattern . "/isU";
        echo span(htmlspecialchars("pattern = " . $pattern));
        if ($this->count == 2) {
            if (preg_match_all($pattern, $pageSource, $output_array)) {
                if (count($output_array[1]) > 1) {
                    my_var_dump($output_array);
                    info("SELECTOR EXISTS IN COUNT " . count($output_array[1]), 'success');
                    $this->selector = $output_array[1][0];
                    if (!$this->save()) my_var_dump($this->errors);
                    return true;

                }
                //  return $output_array[1];
            } else {
                info("SELECTOR DO NOT EXIST " . $this->error->name, 'danger');
                self::throwError($this->error, $pageSource);

                $this->selector = "____ERROR____";
                if (!$this->save()) my_var_dump($this->errors);

            }
        } elseif ($this->count == 1) {
            if (preg_match_all($pattern, $pageSource, $output_array)) {
                if (count($output_array[1]) == 1) {
                    my_var_dump($output_array[1]);
                    info("SELECTOR EXISTS IN COUNT" . count($output_array[1]), 'success');
                    $this->selector = $output_array[1][0];
                    if (!$this->save()) my_var_dump($this->errors);
                    return true;

                } else {
                  //  my_var_dump($output_array[1]);
                    info("SELECTOR DO NOT EXIST", 'danger');
                }
            } else {
                info("SELECTOR DO NOT EXIST", 'danger');
                self::throwError($this->error, $pageSource);
                $this->selector = "____ERROR____";
                if (!$this->save()) my_var_dump($this->errors);
            }
        } elseif ($this->count == Selectors::SELECTOR_CIAN_10) {
            if (preg_match_all($pattern, $pageSource, $output_array)) {
                if (count($output_array[1]) == 2) {
                   // my_var_dump($output_array);
                    info("SELECTOR EXISTS IN COUNT  " . count($output_array[1]), 'success');
                    $this->selector = $output_array[1][0];
                    if (!$this->save()) my_var_dump($this->errors);
                    return true;

                } else {
                    // my_var_dump($output_array);
                    info("SELECTOR DO NOT EXIST", 'danger');
                }
            } else {
                info("SELECTOR DO NOT EXIST", 'danger');
                self::throwError($this->error, $pageSource);
                $this->selector = "____ERROR____";
                if (!$this->save()) my_var_dump($this->errors);
            }
        } elseif ($this->count == Selectors::SELECTOR_CIAN_11) {
            if (preg_match_all($pattern, $pageSource, $output_array)) {
               // my_var_dump($output_array);
                info("SELECTOR EXISTS IN COUNT " . count($output_array[1]), 'success');
                $this->selector = $output_array[1][1];
                if (!$this->save()) my_var_dump($this->errors);
                return true;

            } else {
                info("SELECTOR DO NOT EXIST", 'danger');
                self::throwError($this->error, $pageSource);
                $this->selector = "____ERROR____";
                if (!$this->save()) my_var_dump($this->errors);
            }
         } elseif ($this->count == Selectors::SELECTOR_YANDEX_00) {
            if (preg_match_all($pattern, $pageSource, $output_array)) {
                my_var_dump($output_array[0][0]);
                info("SELECTOR EXISTS IN COUNT " . count($output_array[1]), 'success');
                $this->selector = $output_array[0][0];
                if (!$this->save()) my_var_dump($this->errors);
                return true;

            } else {
                info("SELECTOR DO NOT EXIST", 'danger');
                self::throwError($this->error, $pageSource);
                $this->selector = "____ERROR____";
                if (!$this->save()) my_var_dump($this->errors);
            }
        }


    }

    // перехват нерабочих страниц

    public static function throwError($error, $pageSource,$options = [])
    {
        $ip = \Yii::$app->params['ip'];
        $time = str2url(date("Y-m-d H:i:s"));
        $dir = Yii::getAlias('@app');
        file_put_contents($dir."/web/errors/".$ip."_".$time."_error_" . $error->name . ".html", $pageSource);
        AgentPro::throwError($error);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_sources', 'type', 'pattern', 'count', 'id_error'], 'required'],
            [['id_sources', 'type', 'count', 'id_error', 'id_parent'], 'integer'],
            [['pattern', 'alias', 'selector'], 'string', 'max' => 256],
        ];
    }

    public static function findByAlias($alias)
    {
        return self::find()->where(['alias' => $alias])->one();
    }

    public static function setSelectors($type, $id_source)
    {
        Yii::$app->cache->set("SELECTORS_" . $type . "_" . $id_source,
            ArrayHelper::map(Selectors::find()
                ->where(['id_sources' => $id_source])
                ->andWhere(['type' => $type])
                ->indexBy('alias')->asArray()
                ->all(), 'alias', 'selector'), 10000);
    }


    public static function getSelectors($type, $id_source)
    {
        //$selectors = Yii::$app->cache->get("SELECTORS_" . $type . "_" . $id_source);
        if (!$selectors) {
            self::setSelectors($type, $id_source);
            $selectors = Yii::$app->cache->get("SELECTORS_" . $type . "_" . $id_source);
        };

        return $selectors;
    }

    public function getError()
    {
        return $this->hasOne(Errors::className(), ['id' => 'id_error']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_sources' => 'Source',
            'id_parent' => 'Parent',
            'type' => 'Type',
            'pattern' => 'Pattern',
            'count' => 'count',
            'id_error' => 'Error',
        ];
    }
}
