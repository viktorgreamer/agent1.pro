<?php

namespace app\models;

use app\components\TagsWidgets;
use Yii;

/**
 * This is the model class for table "Velikiy_Novgorod_sale_similar".
 *
 * @property integer $id
 * @property integer $status
 * @property string $similar_ids
 * @property string $similar_ids_all
 * @property string $tags_id
 */
class SaleSimilar extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Velikiy_Novgorod_sale_similar';
    }

// статусы модерации
    const NOT = 1;
    const READY = 2;
    const MODERATED = 3;
    const MODERATION_ONCALL = 4;
    const SALE_SIMILAR_AREA_DIVERGANCE = 10;
    const SALE_SIMILAR_PRICE_DIVERGANCE = 15;

    // статусы активности
    const ACTIVE = 0;
    const DISACTIVE = 1;
    const DISABLED = 2;
    const SOLD = 3;

    // тип списка
    const PUBLIC_TYPE = 1;
    const ALL_TYPE = 0;

    const MODERATION_STATUSES = [
        SaleSimilar::NOT => 'НЕ ПРОМОДЕРИРОВАН',
        SaleSimilar::READY => 'READY',
        SaleSimilar::MODERATED => 'ПРОМОДЕРИРОВАН',
        SaleSimilar::MODERATION_ONCALL => 'ПО ЗВОНКУ',
    ];
    const DISACTIVE_STATUSES = [
        SaleSimilar::ACTIVE => 'ACTIVE',
        SaleSimilar::DISACTIVE => 'DISACTIVE',
        SaleSimilar::DISABLED => 'ПРОПАЛ',
        SaleSimilar::SOLD => 'ПРОДАН',
    ];

    const SELECT_FIELDS = 's.id,s.id_address,s.grossarea,s.id,s.id,s.id,s.id,s.id,s.id,s.id,';


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['similar_ids', 'tags_id'], 'string'],
        ];
    }

    public function generateIdSources()
    {

        $id_sources = Synchronization::find()->select('id_sources')->distinct()->where(['in', 'id', array_unique(array_merge(Tags::convertToArray($this->similar_ids), Tags::convertToArray($this->similar_ids_all)))])->andwhere(['disactive' => Sale::ACTIVE])->column();

        $this->id_sources = Methods::convertToStringWithBorders($id_sources);
    }

    public function getSales()
    {
         if ($this->id) {
             $saleQuery = new SaleQuery();
             return $saleQuery->searchSimilar($this->id)->all();
         }

    }


    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'id',
            'similar_ids' => 'Похожие варианты',
            'tags_id' => 'Теги',
        ];
    }

    public function init()
    {
        $this->moderated = 1;
        $this->status = 0;
        parent::init();
    }

    public function collectTags(array $tags = [])
    {
        $simitar_sales = Sale::find()->select(['tags_id'])->where(['in', 'id', explode(",", $this->similar_ids_all)])->all();
        $common_tags = [];
        $common_tags = array_merge($common_tags, $tags);
        foreach ($simitar_sales as $simitar_sale) {
            $common_tags = array_merge($common_tags, $simitar_sale->tags);
        }
        $common_tags = array_unique($common_tags);
        $this->tags_id = Tags::convertToString($common_tags);
        return $common_tags;
    }

    public function getTags()
    {
        return Tags::convertToArray($this->tags_id);
    }


    public function setTags($insert)
    {
        $this->tags_id = Tags::convertToString($insert);
    }

    public function inject($sale)
    {
        // inject tags
        info('теги были');
        echo TagsWidgets::widget(['tags' => $this->tags]);
        $common_tags = array_unique(array_merge($this->tags, Tags::convertToArray($sale->tags_id)));
        info('теги стали');
        echo TagsWidgets::widget(['tags' => $common_tags]);
        info('IDS были');
        echo $this->similar_ids;
        if (!strpos($this->similar_ids, "," . $sale->id . ",")) $this->similar_ids .= $sale->id . ",";
        info('IDS стали');
        echo $this->similar_ids;
        info('IDS_ALL были');
        echo $this->similar_ids_all;
        if (!strpos($this->similar_ids_all, "," . $sale->id . ",")) $this->similar_ids_all .= $sale->id . ",";
        info('IDS_ALL стали');
        echo $this->similar_ids_all;

    }

    public function removeID($id)
    {
        $this->similar_ids = Tags::convertToString(MyArrayHelpers::DeleteFromArray($id, Tags::convertToArray($this->similar_ids)));
        $this->similar_ids_all = Tags::convertToString(MyArrayHelpers::DeleteFromArray($id, Tags::convertToArray($this->similar_ids_all)));
    }

    public function updateIds()
    {
        if (!empty($similars_id)) $this->similar_ids = Methods::convertToStringWithBorders($similars_id);
        if (!empty($similars_ids_all)) $this->similar_ids_all = Methods::convertToStringWithBorders($similars_ids_all);
    }

    /* @var $sale \app\models\Sale */

    public function processingSimilar($sale)
    {
        echo $sale->renderLong_title();
        echo $sale->renderSource() . " AUTO-SET-SIMILAR ";
        $similar_ids = $sale->searchSimilar();
        $public_similar_ids = $sale->searchPublicSimilar();
        info("SALE_SIMILARS = '" . my_implode($similar_ids) . "'" . " count = " . count($similar_ids), 'alert');
        info("SALE_SIMILARS_public = '" . my_implode($public_similar_ids) . "'" . " count = " . count($public_similar_ids), 'alert');
//        $similar_sales = Synchronization::find()
//            ->where(['in', 'id', $public_similar_ids])
//            ->orderBy('price')
//            ->all();
//       // echo Renders::StaticView('_chart_prices', ['prices' => $sale->searchSimilar('s.price'), 'labels' => $sale->searchSimilar('s.id') ]);
        // echo Renders::StaticView('mini_sale_similar', ['sales' => $similar_sales, 'contacts' => true, 'controls' => true]);
        $sale->createSimilar($public_similar_ids, $similar_ids);
        // $sale->save();
    }


}
