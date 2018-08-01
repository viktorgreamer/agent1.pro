<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 18.05.2018
 * Time: 22:43
 */

namespace app\rbac;


use yii\rbac\Rule;

class AuthorRule extends Rule
{
    public $name = 'Author'; // Имя правила

    public function execute($user_id, $item, $id)
    {
        return isset($id) ? $id == $user_id : false;
    }
}