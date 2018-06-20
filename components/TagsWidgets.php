<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 07.09.2017
 * Time: 23:12
 */

namespace app\components;


use yii\base\Widget;

class TagsWidgets extends Widget

{
    public $tags;
    public $br = true;
    public $id_address;
    public $salefilter_tags_id;
    public $salelist_tags_id;
    public $agent_id;
    public $publish;
    public $moderate;

    public function run()
    {

        return $this->render('tags/render-tags',
            [
                'tags' => $this->tags,
                'br' => $this->br,
                'id_address' => $this->id_address,
                'salefilter_tags_id' => $this->salefilter_tags_id,
                'salelists_tag_id' => $this->salelist_tags_id,
                'publish' => $this->publish,
                'moderate' => $this->moderate,
                'agent_id' => $this->agent_id
                ]);
    }
}