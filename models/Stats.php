<?php
/**
 * Created by PhpStorm.
 * User: Анастсия
 * Date: 27.07.2018
 * Time: 6:11
 */

namespace app\models;


class Stats
{



    const TYPE_LIVE = 1;
    const TYPE_ALL = 2;

    public static function setPrefixies($prefix) {
        Control::setPrefixies($prefix);
    }

    public static function AgentsCount()
    {
        return Agents::find()->where(['person_type' => Agents::PERSON_TYPE_AGENT])->groupBy('phone')->count();

    }

    public static function HouseKeepersCount()
    {
        return Sale::find()
            ->from(['s' => Sale::tableName()])
            ->joinWith(['agent AS agent'])
            ->joinWith(['similar AS similar'])
            ->where(['agent.person_type' => Agents::PERSON_TYPE_HOUSEKEEPER])
            ->andWhere(['disactive' => SaleSimilar::ACTIVE])
            ->orderBy(['id_similar'])
            ->count();
    }

    public static function countSale($options = [])
    {
        $query = Sale::find()
            ->from(['s' => Sale::tableName()])
            ->joinWith(['agent AS agent'])
            ->joinWith(['similar AS similar']);
        if ($options['disactive']) {
            info("OPTIONS DISACTIVE");
            $query->where(['s.disactive' => $options['disactive']]);
        }
        if ($options['disactive'] === null) {
            info("OPTIONS DISACTIVE NULL");
            $query->where(['IS', 's.disactive', $options['disactive']]);
        }
        if ($options['id_sources']) $query->andWhere(['s.id_sources' => $options['id_sources']]);
        if ($options['date_from']) $query->andWhere(['>', 's.date_start', $options['date_from']]);
        if ($options['date_to']) $query->andWhere(['>', 's.date_start', $options['date_to']]);
        if ($options['status']) $query->andWhere(['similar.status' => $options['status']]);

        $query->groupBy('s.id');

        return $query->count();
    }
}