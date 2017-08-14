<?php

namespace Bankiru\Api\JsonRpc\Specification;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use Bankiru\Api\JsonRpc\Exception\InvalidRequestException;
use Bankiru\Api\JsonRpc\Exception\JsonRpcException;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

/** @internal */
final class JsonRpcRequest implements JsonRpcRequestInterface
{
    /** @var  string|null */
    private $id;
    /** @var  string */
    private $method;
    /** @var  mixed|\stdClass */
    private $parameters;

    /**
     * @param \stdClass $source
     *
     * @return JsonRpcRequestInterface
     * @throws JsonRpcException
     */
    public static function fromStdClass(\stdClass $source)
    {
        $request = new static;

        $missing = [];

        foreach (['method', 'jsonrpc'] as $field) {
            if (!isset($source->$field)) {
                $missing[] = $field;
            }
        }
        if (count($missing) > 0) {
            throw InvalidRequestException::missingFields($missing);
        }

        if (BankiruJsonRpcServerBundle::JSONRPC_VERSION !== $source->jsonrpc) {
            throw InvalidRequestException::invalidVersion(
                BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                $source->jsonrpc
            );
        }

        $request->id         = isset($source->id) ? $source->id : null;
        $request->method     = $source->method;
        $request->parameters = isset($source->params) ? json_decode(json_encode($source->params), true) : null;

        return $request;
    }

    /** {@inheritdoc} */
    public function isNotification()
    {
        return null === $this->id;
    }

    /** {@inheritdoc} */
    public function jsonSerialize()
    {
        return [
            self::VERSION_FIELD    => $this->getVersion(),
            self::METHOD_FIELD     => $this->getMethod(),
            self::PARAMETERS_FIELD => $this->getParameters(),
            self::ID_FIELD         => $this->getId(),
        ];
    }

    /** {@inheritdoc} */
    public function getVersion()
    {
        return BankiruJsonRpcServerBundle::JSONRPC_VERSION;
    }

    /** @return string */
    public function getMethod()
    {
        return $this->method;
    }

    /** {@inheritdoc} */
    public function getParameters()
    {
        return $this->parameters;
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->id;
    }
}
