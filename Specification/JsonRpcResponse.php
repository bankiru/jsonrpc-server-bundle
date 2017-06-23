<?php

namespace Bankiru\Api\JsonRpc\Specification;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use ScayTrase\Api\Rpc\RpcErrorInterface;

/** @internal */
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

    /** {@inheritdoc} */
    public function getVersion()
    {
        return BankiruJsonRpcServerBundle::VERSION;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        $result = [
            self::VERSION_FIELD => BankiruJsonRpcServerBundle::VERSION,
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

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }

    /** {@inheritdoc} */
    public function isSuccessful()
    {
        return $this->getError() === null;
    }

    /** {@inheritdoc} */
    public function getError()
    {
        return $this->error;
    }

    /** {@inheritdoc} */
    public function getBody()
    {
        return $this->body;
    }
}
