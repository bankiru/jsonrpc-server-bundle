<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 15:25
 */

namespace Bankiru\Api\JsonRpc\Specification;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

final class JsonRpcRequest implements JsonRpcRequestInterface
{
    /** @var  string|null */
    private $id;
    /** @var  string */
    private $method;
    /** @var  mixed|\stdClass */
    private $parameters;

    /** @return int|null Id. if not a notification and id is not set - id should be automatically generated */
    public function getId()
    {
        return $this->id;
    }

    /** @return bool True if request should not receive response from the server */
    public function isNotification()
    {
        return null === $this->id;
    }

    /** @return string */
    public function getMethod()
    {
        return $this->method;
    }

    /** @return array */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param \stdClass $source
     *
     * @return JsonRpcRequestInterface
     * @throws \InvalidArgumentException
     */
    public static function fromStdClass(\stdClass $source)
    {
        $request = new static;

        if (!isset($source->method, $source->jsonrpc)) {
            throw new \InvalidArgumentException('Not a valid decoded JSON RPC');
        }

        assert('2.0' === $source->jsonrpc);

        $request->id         = isset($source->id) ? $source->id : null;
        $request->method     = $source->method;
        $request->parameters = isset($source->params) ? json_decode(json_encode($source->params), true) : null;

        return $request;
    }

    /** {@inheritdoc} */
    public function getVersion()
    {
        return JsonRpcBundle::VERSION;
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
}
