<?php

namespace Bankiru\Api\JsonRpc\Test\Entity;

use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Since;
use JMS\Serializer\Annotation\Type;

class SampleEntity
{
    /**
     * @var int|null
     */
    private $id;
    /**
     * @var  string
     * @Type("string")
     * @SerializedName("sample_payload")
     * @Expose()
     */
    private $payload;
    /**
     * @var  string
     * @Type("string")
     * @SerializedName("private_payload")
     * @Expose()
     * @Groups({"private"})
     * @Since("0.1")
     */
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
}
