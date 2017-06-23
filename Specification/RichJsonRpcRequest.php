<?php

namespace Bankiru\Api\JsonRpc\Specification;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use Bankiru\Api\Rpc\Http\RequestInterface;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

final class RichJsonRpcRequest implements RequestInterface, JsonRpcRequestInterface
{
    /** @var  JsonRpcRequestInterface */
    private $request;
    /** @var  ParameterBag */
    private $attributes;

    /**
     * RichJsonRpcRequest constructor.
     *
     * @param JsonRpcRequestInterface $request
     * @param ParameterBag            $attributes
     */
    public function __construct(JsonRpcRequestInterface $request, ParameterBag $attributes = null)
    {
        $this->request    = $request;
        $this->attributes = $attributes ?: new ParameterBag();
    }

    /** {@inheritdoc} */
    public function isNotification()
    {
        return $this->request->isNotification();
    }

    /** {@inheritdoc} */
    public function getAttributes()
    {
        return $this->attributes;
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
        return BankiruJsonRpcServerBundle::VERSION;
    }

    /** {@inheritdoc} */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /** {@inheritdoc} */
    public function getParameters()
    {
        return $this->request->getParameters();
    }

    /** {@inheritdoc} */
    public function getId()
    {
        return $this->request->getId();
    }
}
