<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 02.08.2018
 * Time: 13:18
 */
foreach ([2,3,4,5,8,12,56] as $tag_id) {
    $tag = \app\models\Tags::findOne($tag_id);
    echo \app\models\Tags::renderActiveTagNewer(1,$tag,[2,5,12],'sale');

}
