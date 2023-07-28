<?php

namespace Rosey\TrendingTopics\Entity;

use XF\Mvc\Entity\Structure;

class TrendingTopics extends \XF\Mvc\Entity\Entity
{
    public static function getStructure(Structure $structure)
{
    $structure->table = 'xf_rosey_trending_topics';
    $structure->shortName = 'Rosey\TrendingTopics:TrendingTopics';
    $structure->primaryKey = 'trending_topic_id';
    $structure->columns = [
        'trending_topic_id' => ['type' => self::UINT, 'required' => true],
        'thread_id' => ['type' => self::UINT, 'required' => true],
        'number_of_reactions' => ['type' => self::UINT, 'required' => true],
        'number_of_replies' => ['type' => self::UINT, 'required' => true],
    ];
    $structure->getters = [];
    $structure->relations = [
        'Thread' => [
            'entity' => 'XF:Thread',
            'type' => self::TO_ONE,
            'conditions' => 'thread_id',
            'primary' => true
        ],
    ];

    return $structure;
}

}