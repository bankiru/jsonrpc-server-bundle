<?php

namespace Bankiru\Api\JsonRpc\Specification;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use ScayTrase\Api\Rpc\RpcErrorInterface;

final class JsonRpcResponse implements JsonRpcResponseInterface
{
    /** @var  string */
    private $id;
    /** @var  \stdClass|\stdClass[]|null */
    private $body;
    /** @var  RpcErrorInterface|null */
    private $error;

    /**
     * JsonRpcResponse constructor.
     *
     * @param string                     $id
     * @param null|\stdClass|\stdClass[] $body
     * @param null|RpcErrorInterface     $error
     */
    public function __construct($id = null, $body = null, RpcErrorInterface $error = null)
    {
        $this->id    = $id;
        $this->body  = $body;
        $this->error = $error;
    }


    /**
     * @return string JSON-RPC version
     */
    public function getVersion()
    {
        return JsonRpcBundle::VERSION;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $result = [
            self::VERSION_FIELD => JsonRpcBundle::VERSION,
            self::ID_FIELD      => $this->getId(),
        ];

        if ($this->isSuccessful()) {
            $result[self::RESULT_FIELD] = $this->getBody();
        }

        if (!$this->isSuccessful()) {
            $result[self::ERROR_FIELD] = $this->getError();
        }

        return $result;
    }

    /** @return string|null Response ID or null for notification pseudo-response */
    public function getId()
    {
        return $this->id;
    }

    /** @return bool */
    public function isSuccessful()
    {
        return $this->getError() === null;
    }

    /** @return RpcErrorInterface|null */
    public function getError()
    {
        return $this->error;
    }

    /** @return \stdClass|\stdClass[]|null */
    public function getBody()
    {
        return $this->body;
    }
}
