<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
class Relation
{
    public function getHandler()
    {
        return 'Relation';
    }
}
