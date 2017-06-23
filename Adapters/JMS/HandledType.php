<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS;

/**
 * @Annotation
 * @Target("PROPERTY")
 */
abstract class HandledType
{
    public $handler;

    /**
     * HandledType constructor.
     *
     * @param $handler
     */
    public function __construct($handler = 'Relation') { $this->handler = $handler; }

    /**
     * @return string
     */
    public function getHandler()
    {
        return $this->handler;
    }
}
