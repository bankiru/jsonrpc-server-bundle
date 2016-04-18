<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 18:51
 */

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
