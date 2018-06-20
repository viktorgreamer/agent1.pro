<?php

namespace app\models;

use Yii;
use app\models\MyArrayHelpers;

/**
 * This is the model class for table "{{%Velikiy_Novgorod_tags}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer tag_id
 * @property integer $sale_id
 * @property integer $salefilter_id
 */
class RealTags extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    private static $tablePrefix;

    public static function tableName()
    {
        if (self::$tablePrefix) {
            return self::$tablePrefix . '_tags';
        } else {
            // Если префикс не устанавливался - стучимся за ним в сессию
            $session = Yii::$app->session;
            return $session->get('city_module') . "_tags";
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
            [['tag_id'], 'required'],
            [['user_id', 'sale_id', 'tag_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'tag_id' => 'Tag ID',
            'sale_id' => 'Sale ID',
            'salefilter_id' => 'Salefilter ID',
        ];
    }

    public static function setRealTags_old($sale_id, $user_id, $tag_id)
    {
        // ищем tags данного sale по sale_id
        $RealTags = RealTags::find()->where(['sale_id' => $sale_id])->one();
        // если какие-то теги на данный sale есть в принципе то редактируем их
        if ($RealTags) {
            // если в списке что-то есть
            if ($RealTags->tags_id != '') {
                // получаем список уже имеющихся tags
                $exist_tags = explode(',', $RealTags->tags_id);
                // если там данный tag есть то удаляем его если нет то добавляем
                if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                else  array_push($exist_tags, $tag_id);
                // переводим массив в список
                if (count($exist_tags) == 0) $RealTags->tags_id = ''; else $RealTags->tags_id = implode(",", $exist_tags);

            } else $RealTags->tags_id = $tag_id; // если ничего не было, то сразу добавляем

            // сохраняем данный
            $RealTags->save();
        } else {
            $RealTags = New RealTags();
            $RealTags->sale_id = $sale_id;
            // $RealTags->user_id = $user_id;
            $RealTags->user_id = 0;
            $RealTags->tags_id = $tag_id;
            $RealTags->save();


        }


    }

    public static function setRealTags($parent_id, $tag_id, $type = 'sale', $user_id)
    {
        switch ($type) {
            case 'sale': {
//                // ищем tags данного sale по sale_id
//                $RealTags = RealTags::find()->where(['sale_id' => $parent_id])->andWhere(['tag_id' => $tag_id])->one();
//                // если какие-то теги на данный sale есть в принципе то редактируем их
//                if ($RealTags) $RealTags->delete();
//                else {
//                    $RealTags = New RealTags();
//                    $RealTags->sale_id = $parent_id;
//                    $RealTags->tag_id = $tag_id;
//                    $RealTags->save();
//                }
                $sale = Sale::findOne($parent_id);
                if ($sale) {
                    if ($sale->id_similar) {
                        $tag = Tags::findOne($tag_id);
                        $a_type = $tag->a_type;
                        echo "<br>TAG: ".$tag->id." ".$tag->name." a_TYPE ".$tag->a_type;
                        if ($tag->a_type) {
                            echo "<br> ЕСТЬ ДОПОЛНИТЕЛЬНЫЙ ТИП<br>";
                            //  echo $sale->similarNew->tags_id;
                            $all_tags = Tags::find()->where(['in','id', $sale->similarNew->tags])->all();
                            foreach ($all_tags as $tag) {
                                echo "<br>".$tag->id." ".$tag->name." a_TYPE ".$tag->a_type;
                            }
                            // вычисляем смежные tags
                            $all_tags_splitted = Tags::find()->select('id')->where(['in','id', MyArrayHelpers::DeleteFromArray($tag_id, $sale->similarNew->tags)])->andWhere(['a_type' => $a_type])->column();
                            echo "<br>all_tags_splitted<br>";
                            my_var_dump($all_tags_splitted);
                            echo "<br>ORIGINAL: ".$sale->similarNew->tags_id;
                            echo "<br>DUPLICATES: ".Tags::convertToString($all_tags_splitted);
                            $new_array = array_diff($sale->similarNew->tags, $all_tags_splitted);
                            echo "<br>NEW ARRAY: ".Tags::convertToString($new_array);
                            echo "<br>";
                            echo "<br>";
                            echo "<br>";
                        } else $new_array = $sale->similarNew->tags;


                        $similar = SaleSimilar::findOne($sale->id_similar);
                        $similar->setTags(MyArrayHelpers::AddOrDelete($new_array, $tag_id));
                         // $similar->setTags(MyArrayHelpers::AddOrDelete($similar->tags, $tag_id));
                        echo "<br>".$similar->tags_id;
                        foreach (Tags::find()->where(['in', 'id', Tags::convertToArray($similar->tags_id)])->all() as $tag) {
                            echo "<br>".$tag->id." ".$tag->name." a_TYPE ".$tag->a_type;
                        }
                        $similar->save();
                    }
                    else {
                    //  echo $sale->tags_id;
                        $sale->setTags(MyArrayHelpers::AddOrDelete($sale->tags, $tag_id));
                        $sale->sync = 2;
                        if (!$sale->save()) return $sale->getErrors();
                    }

                }
                return $sale->tags_id;
            }
            case 'address': {
                // ищем tags данного sale по sale_id
//                $RealTags = RealTags::find()->where(['id_address_tag' => $parent_id])->andWhere(['tag_id' => $tag_id])->one();
//                // если какие-то теги на данный sale есть в принципе то редактируем их
//                if ($RealTags) $RealTags->delete();
//                else {
//                    $RealTags = New RealTags();
//                    $RealTags->id_address_tag = $parent_id;
//                    $RealTags->tag_id = $tag_id;
//                    $RealTags->save();
//                }
                $address = Addresses::findOne($parent_id);
                if ($address) {
                    $address->setTags(MyArrayHelpers::AddOrDelete($address->tags, $tag_id));
                    if (!$address->save()) return my_var_dump($address->getErrors());
                }
                break;
            //    return $address->tags_id;
            }
            case 'salefilter': {

                $salefilter = SaleFilters::findOne($parent_id);
                if ($salefilter) {
                    $salefilter->setTags(MyArrayHelpers::AddOrDelete($salefilter->tags, $tag_id));
                    if (!$salefilter->save(false)) return my_var_dump($salefilter->getErrors());
                }
                break;
              //  return $address->tags_id;
            }
            case 'plus_search': {

                $session = Yii::$app->session;

                $current_tags = $session->get('tags_id_to_search_sale');

                if ($current_tags != '') {
                    //  echo " если в списке что-то есть";
                    // получаем список уже имеющихся tags
                    $exist_tags = explode(',', $current_tags);
                    // если там данный tag есть то удаляем его если нет то добавляем
                    if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                    else  array_push($exist_tags, $tag_id);
                    // переводим массив в список
                    if (count($exist_tags) == 0) $current_tags = ''; else $current_tags = implode(",", $exist_tags);

                } else {
                    // echo " список был пустой";
                    $current_tags = $tag_id;
                } // если ничего не было, то сразу добавляем
                $session->set('tags_id_to_search_sale', $current_tags);
                return $current_tags;
            }
            case 'minus_search': {

                $session = Yii::$app->session;

                $current_tags = $session->get('minus_tags_id_to_search_sale');

                if ($current_tags != '') {
                    //  echo " если в списке что-то есть";
                    // получаем список уже имеющихся tags
                    $exist_tags = explode(',', $current_tags);
                    // если там данный tag есть то удаляем его если нет то добавляем
                    if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                    else  array_push($exist_tags, $tag_id);
                    // переводим массив в список
                    if (count($exist_tags) == 0) $current_tags = ''; else $current_tags = implode(",", $exist_tags);

                } else {
                    // echo " список был пустой";
                    $current_tags = $tag_id;
                } // если ничего не было, то сразу добавляем
                $session->set('minus_tags_id_to_search_sale', $current_tags);
                return $current_tags;
            }

        }
    }


    public function setToAddress($address_id, $tag_id)
    {

        if ($address_id == 1000000) {
            $session = Yii::$app->session;
            $tags_id = $session->get('tags_id');

            // если в списке что-то есть
            if ($tags_id != '') {
                // получаем список уже имеющихся tags
                $exist_tags = explode(',', $tags_id);
                // если там данный tag есть то удаляем его если нет то добавляем
                if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                else  array_push($exist_tags, $tag_id);
                // переводим массив в список
                if (count($exist_tags) == 0) $tags_id = ''; else $tags_id = implode(",", $exist_tags);

            } else $tags_id = $tag_id; // если ничего не было, то сразу добавляем

            // сохраняем данный address
            $session->set('tags_id', $tags_id);
        } elseif ($address_id == 2000000) {
            $session = Yii::$app->session;
            $tags_id = $session->get('tags_id_to_search');

            // если в списке что-то есть
            if ($tags_id != '') {
                // получаем список уже имеющихся tags
                $exist_tags = explode(',', $tags_id);
                // если там данный tag есть то удаляем его если нет то добавляем
                if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                else  array_push($exist_tags, $tag_id);
                // переводим массив в список
                if (count($exist_tags) == 0) $tags_id = ''; else $tags_id = implode(",", $exist_tags);

            } else $tags_id = $tag_id; // если ничего не было, то сразу добавляем

            // сохраняем данный address
            $session->set('tags_id_to_search', $tags_id);
        } else {
            $address = Addresses::findOne($address_id);
            // старый метод
            /* // если в списке что-то есть
             if ($address->tags_id != '') {
                 // получаем список уже имеющихся tags
                 $exist_tags = explode(',', $address->tags_id);
                 // если там данный tag есть то удаляем его если нет то добавляем
                 if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
                 else  array_push($exist_tags, $tag_id);
                 // переводим массив в список
                 if (count($exist_tags) == 0) $address->tags_id = ''; else $address->tags_id = implode(",", $exist_tags);

             } else $address->tags_id = $tag_id; // если ничего не было, то сразу добавляем

             // сохраняем данный address
             $address->save();*/

            $existed_tag = RealTags::find()->where(['id_address_tag' => $address->id])->andWhere(['tag_id' => $tag_id])->one();
            if ($existed_tag) {
                $existed_tag->delete();
                return " удалили tag=" . $tag_id;
            } else {

                $realtag = new RealTags();
                $realtag->tag_id = $tag_id;
                $realtag->id_address_tag = $address->id;
                if (!$realtag->save()) my_var_dump($realtag->getErrors());
                return " set tag=" . $tag_id;
            }
        }


    }


    public function setToSaleFilter($salefilter_id, $tag_id)
    {
        $salefilter = SaleFilters::findOne($salefilter_id);

        // если в списке что-то есть
        if ($salefilter->tags_id != '') {
            //  echo " если в списке что-то есть";
            // получаем список уже имеющихся tags
            $exist_tags = explode(',', $salefilter->tags_id);
            // если там данный tag есть то удаляем его если нет то добавляем
            if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
            else  array_push($exist_tags, $tag_id);
            // переводим массив в список
            if (count($exist_tags) == 0) $salefilter->tags_id = ''; else $salefilter->tags_id = implode(",", $exist_tags);

        } else {
            // echo " список был пустой";
            $salefilter->tags_id = $tag_id;
        } // если ничего не было, то сразу добавляем

        // сохраняем данный address
        $salefilter->save();

    }

    public function setToSaleList($salelist_id, $tag_id)
    {
        $salelist = SaleLists::findOne($salelist_id);

        // если в списке что-то есть
        if ($salelist->tags_id != '') {
            //  echo " если в списке что-то есть";
            // получаем список уже имеющихся tags
            $exist_tags = explode(',', $salelist->tags_id);
            // если там данный tag есть то удаляем его если нет то добавляем
            if (in_array($tag_id, $exist_tags)) unset($exist_tags[array_search($tag_id, $exist_tags)]);
            else  array_push($exist_tags, $tag_id);
            // переводим массив в список
            if (count($exist_tags) == 0) $salelist->tags_id = ''; else $salelist->tags_id = implode(",", $exist_tags);

        } else {
            // echo " список был пустой";
            $salelist->tags_id = $tag_id;
        } // если ничего не было, то сразу добавляем

        // сохраняем данный address
        $salelist->timestamp = time();
        $salelist->save();

    }

}
