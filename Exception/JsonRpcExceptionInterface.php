<?php

namespace Bankiru\Api\JsonRpc\Exception;

use ScayTrase\Api\JsonRpc\JsonRpcErrorInterface;

interface JsonRpcExceptionInterface
{
    /** @return JsonRpcErrorInterface */
    public function getJsonRpcError();
}
