<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 18:51
 */

namespace Bankiru\Api\JsonRpc\Exception;

use ScayTrase\Api\JsonRpc\JsonRpcErrorInterface;

interface JsonRpcExceptionInterface
{
    /** @return JsonRpcErrorInterface */
    public function getJsonRpcError();
}
