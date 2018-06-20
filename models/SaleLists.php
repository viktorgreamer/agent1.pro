<?php

namespace app\models;

use Yii;
use app\models\SimpleImage;
use yii\helpers\ArrayHelper;
use Eventviva\ImageResize;

/**
 * This is the model class for table "{{%sale_lists}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $list_of_ids
 * @property string $komment
 * @property string $tags
 * @property string $similar_lists
 * @property string $parent_salefilter
 * @property string $ids_ok
 * @property string $ids_ban
 */
class SaleLists extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_sale_lists';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_sale_lists";
        }
    }

    public static function setTablePrefix($prefix)
    {
        self::$tablePrefix = $prefix;
    }

    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type'], 'integer'],

            [['list_of_ids', 'name', 'komment','regions','parent_salefilter', 'ids_ok','ids_ban'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function Exists()
    {
        return SaleLists::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['name' => $this->name])
            ->exists();
    }

    /**
     * @inheritdoc
     */
    public function FindExisted()
    {
        $salelist = SaleLists::find()
            ->where(['user_id' => $this->user_id])
            ->andWhere(['name' => $this->name])
            ->one();
        return $salelist->id;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'name',
            'list_of_ids' => 'List Of Ids',
        ];
    }

    public function ExportIrr()
    {
        $sales = Sale::find()
            ->where(['in', 'id', explode(",", $this->list_of_ids)])
            ->limit(50)
            ->all();
        // echo " всего объявлений" . count($sales);
        $session = Yii::$app->session;
        $user_id = $session->get('user_id');


        $main_dir = "foto_" . $user_id . "_" . $this->id;

        $irr_user = $session->get('irr_id_partners');
// создаем дату на 10 дней вперед
        $today = date("Y-m-d", time());
        $today .= 'T';
        $today .= date("H:i:s", time());
        $today10 = date("Y-m-d", time() + 10 * 24 * 60 * 60);
        $today10 .= 'T';
        $today10 .= date("H:i:s", time() + 10 * 24 * 60 * 60);


        $response = "<?xml version=\"1.0\" encoding=\"utf-8\"?>";

        $response .= " <users>
         <user deactivate-untouched=\"false\">
        <match><user-id>{$irr_user}</user-id></match>";
// побегаемся по вcем sale
        foreach ($sales as $sale) {
            $response .= " <store-ad validfrom = \"" . $today . "\" validtill = \"" . $today10 . "\" power-ad = \"1\" source-id = \"" . $sale->id . "\" category = \"/real-estate/apartments-sale/secondary\"
        
                  adverttype = \"realty_sell\" >";
//            <products >
//                <product name = "premium" type = "7" validfrom = "2013-12-03" />
//            </products >
            $response .= " <price value=\"" . $sale->price . "\" currency = \"RUR\" ></price>
            <title> " . $sale->title . " </title>
    
            <description> " . $sale->description . "</description>\n";
            // заполняем фото
            $images = explode("X", $sale->images);
            if (count($images) > 0) {
                $response .= "<fotos>";
                $n = 0;
                foreach ($images as $image) {
                    //домен
                    $web_dir_foto = "http://a0086640.xsph.ru/export_irr";
                    if (!empty($image)) {
                        $n++;
                        // уникальное название файла
                        $name = array_pop(explode("/", $image));
                        // уникальные каталог с фотографиями
                        $dir = my_transliterate($sale->id . "_" . $sale->rooms_count . "_" . preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $sale->address));
                        // полное имя до файла в web
                        $web_name = $web_dir_foto . "/" . $main_dir . "/" . $dir . "/" . $name;
                        // локальное имя до файла
                        $local_name = $main_dir . "/" . $dir . "/" . $name;
                        if (file_exists($local_name)) {
                            $md_5 = md5_file($local_name);
                            $response .= " <foto-remote url=\"" . $web_name . "\" md5 = \"" . $md_5 . "\"/>\n";
                        }
                        if ($n = 10) break;
                    }
                }
                $response .= "</fotos>";
            }

            $address = Addresses::findOne($sale->id_address);
            if (!empty($address->hull)) $house_hull = $address->house . " " . $address->hull;
            else $house_hull = $address->house;
            $response .= "<custom-fields>
                <field name=\"region\"> Новгородская </field>
                <field name=\"address_city\"> Великий Новгород</field>
                <field name=\"address_street\"> " . $address->street . "</field>
                <field name=\"address_house\"> " . $house_hull . "</field>
                <field name=\"phone\"> +7(963)240-99-45</field>
                <field name=\"contact\">Виктор</field>
                <field name=\"etage\"> " . $sale->floor . "</field>
                <field name=\"etage-all\"> " . $sale->floorcount . "</field>
                <field name=\"rooms\">" . $sale->rooms_count . "</field>
                <field name=\"meters-total\"> " . $sale->grossarea . "</field>
                <field name=\"meters-living\">" . $sale->living_area . "</field>
                <field name=\"kitchen\">" . $sale->kitchen_area . "</field>
                          </custom-fields>
        </store-ad>";

        }
        // закрываем user
        $response .= " </user ></users>";
        //echo $response;
        $fh = fopen($main_dir . "/irr.xml", 'w');
        fwrite($fh, $response);

    }

    public function getTags() {
        if ($this->tags_id) return explode(",", $this->tags_id);
        else return false;
    }

    public function LoadPhotoToLocal()
    {

        $session = Yii::$app->session;
        $user_id = $session->get('user_id');

        $main_dir = "foto_" . $user_id . "_" . $this->id;
        $sales = Sale::find()
            ->where(['in', 'id', explode(",", $this->list_of_ids)])
            ->limit(50)
            ->all();
        echo " всего объявлений" . count($sales);
        if (!file_exists($main_dir)) {
            echo " создаем директорию";
            mkdir($main_dir);
        }
        foreach ($sales as $sale) {
            echo " <br>";
            echo $sale->id . " list of photo <a href='" . $sale->url . "'> link </a>";
            // echo " <br>";
            $links = unserialize($sale->images);

            foreach ($links as $link) {
                //  echo " <br>";
                // echo $link;
                $name = array_pop(explode("/", $link));
                // echo " name = " . $name . " sale address" . $sale->address;

               // $dir = $main_dir . "/" . my_transliterate($sale->id . "_" . $sale->rooms_count . "_" . preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $sale->address));
                $dir = $main_dir . "/" . ($sale->id . "_" . $sale->rooms_count . "_" . preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/", "", $sale->address));
                $is_download = false;
                //если такой директории еще не существует то создаем ее и
                if (!file_exists($dir)) {
                    echo " создаем директорию";
                    mkdir($dir);
                }
                if (!file_exists($dir . "/" . $name)) {

                    if ($name != '') {
                        $target_url = str_replace('640x480', '1280x960', $link);


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
                                $image = new ImageResize($dir . "/" . $name);
                                if ($height >= $width) $image->crop($width, $height - 50);
                                else $image->crop($width - 100, $height);
                                $image->save($dir . "/" . $name);
                            } else {
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
                    $n++;
                }

            }
            // скачиваем пачками по 10 шт.

            // if ($n > 10) break;
        }


    }

    public function addItem($id_item)
    {
        $this->list_of_ids = implode(",", (explode(",", $this->list_of_ids) + $id_item));
        $this->timestamp = time();
        $this->save();
    }

    public function addArrayOfItem($ids_item)
    {
        if (!empty($ids_item)) {
            if (is_array($ids_item)) {
                $this->list_of_ids = implode(",", array_unique(array_merge(explode(",", $this->list_of_ids) + $ids_item), SORT_STRING));
            } else {
                $existList = explode(",", $this->list_of_ids);
                array_push($existList, $ids_item);
                $this->list_of_ids = implode(",", array_unique($existList, SORT_STRING));
            }
            $this->timestamp = time();
            $this->save();
        }

    }

    /*
     *
     * ssda*/

    public static function getMyListsAsArray($types = 0)
    {
        if (!$types) {
            $session = Yii::$app->session;
            $user_id = $session->get('user_id');
            return ArrayHelper::map(SaleLists::find()->where(['user_id' => $user_id])->all(), 'id', 'name');
        } else {
            $session = Yii::$app->session;
            $user_id = $session->get('user_id');
            return ArrayHelper::map(SaleLists::find()->where(['user_id' => $user_id])->andWhere(['in', 'type', $types])->all(), 'id', 'name');
        }

    }

    public function getMinPrice()
    {
        if ($this->list_of_ids != '') {
            $min = Sale::find()->where(['in', 'id', explode(",", $this->list_of_ids)])->min('price');
            return $min;
        }
    }

    public function getMaxPrice()
    {
        if ($this->list_of_ids != '') {
            $min = Sale::find()->where(['in', 'id', explode(",", $this->list_of_ids)])->max('price');
            return $min;
        }
    }

    public function getCount()
    {
        return count(explode(",", $this->list_of_ids));

    }

    public function getRelevantedByTags($limit = 5, $type = 'public')
    {
        $tags = explode(",", $this->tags_id);
        if ($type == 'public-sold') $SaleLists = SaleLists::find()->where(['type' => 6])->all();
        $SaleLists = SaleLists::find()->where(['type' => 2])->all();
        $tags_array_counter = [];
        foreach ($SaleLists as $saleList) {
            $n = 0;
            foreach ($tags as $tag) {
                if (in_array($tag, explode(",", $saleList->tags_id))) $n++;
            }
            $tags_array_counter[$saleList->id] = $n;

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

    /*
     * This function makes search lists relevanted by same locality.
     * */
    public function getLocalityLists($limit = 5, $type = 'public')
    {
        return SaleLists::find()->where(['regions' => $this->regions])->limit($limit)->orderBy('name')->all();

    }

    /*
     * данный метод выводит все фильтры-аггрегаторы, которые являются родителями данного списка (если $type='all', то вообще все  фильтры-аггрегаторы)
     * */
    public function getParentsSaleFilters($type = 'all')
    {
        if ($type == 'all') {
            $salefilters = SaleFilters::find()->where(['in','type', [1,3]])->all();
            return ArrayHelper::map($salefilters, 'id', 'name');

        } else {
            if ($this->parent_salefilter) {
                $salefilters = SaleFilters::find()->where(['in', 'id', explode(",",$this->parent_salefilter)])->all();
                if ($salefilters) return ArrayHelper::map($salefilters, 'id', 'name');
            } else return false;
        }
    }


}
