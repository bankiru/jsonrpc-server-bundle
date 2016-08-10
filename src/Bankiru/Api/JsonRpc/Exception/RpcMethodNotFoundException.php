<?php

namespace Bankiru\Api\JsonRpc\Exception;

use Bankiru\Api\Rpc\Routing\Exception\MethodNotFoundException;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcErrorInterface;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

class RpcMethodNotFoundException extends MethodNotFoundException implements JsonRpcExceptionInterface
{
    /** @var  JsonRpcRequestInterface */
    private $error;

    /**
     * RpcMethodNotFoundException constructor.
     *
     * @param string    $method
     * @param \stdClass $data
     */
    public function __construct($method, \stdClass $data = null)
    {
        parent::__construct($method);

        $this->error =
            new JsonRpcError(
                JsonRpcError::METHOD_NOT_FOUND,
                $this->getMessage(),
                $data
            );
    }

    /** @return JsonRpcErrorInterface */
    public function getJsonRpcError()
    {
        return $this->error;
    }
}
