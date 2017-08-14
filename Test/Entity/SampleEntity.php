<?php

namespace Bankiru\Api\JsonRpc\Test\Entity;

class SampleEntity implements \JsonSerializable
{
    /** @var int|null */
    private $id;
    /** @var  string */
    private $payload;

    /** @var  string */
    private $secret;

    /**
     * SampleEntity constructor.
     *
     * @param string $payload
     */
    public function __construct($payload)
    {
        $this->payload = $payload;
        $this->secret  = 'secret-payload';
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        return [
            'id'             => $this->id,
            'sample_payload' => $this->payload,
        ];
    }
}
