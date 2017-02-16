<?php

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

    public static function create($code, $message, $data = null)
    {
        $ex               = new static($message);
        $ex->jsonRpcError = new JsonRpcError($code, $message, $data);

        return $ex;
    }
}
