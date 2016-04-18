<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 18:51
 */

namespace Bankiru\Api\JsonRpc\Exception;

use Exception;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcErrorInterface;

class JsonRpcException extends \Exception implements JsonRpcExceptionInterface
{
    /** @var  JsonRpcErrorInterface */
    private $jsonRpcError;

    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        $this->jsonRpcError = new JsonRpcError(JsonRpcError::INTERNAL_ERROR, $message, (object)['x-code' => $code]);

        parent::__construct($message, $code, $previous);
    }


    /**
     * @return JsonRpcErrorInterface
     */
    public function getJsonRpcError()
    {
        return $this->jsonRpcError;
    }

    /**
     * @param JsonRpcErrorInterface $jsonRpcError
     */
    protected function setJsonRpcError(JsonRpcErrorInterface $jsonRpcError)
    {
        $this->jsonRpcError = $jsonRpcError;
    }
}
