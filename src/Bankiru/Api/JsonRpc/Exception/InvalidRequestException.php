<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 17.05.2016
 * Time: 11:38
 */

namespace Bankiru\Api\JsonRpc\Exception;

use ScayTrase\Api\JsonRpc\JsonRpcError;

class InvalidRequestException extends JsonRpcException
{
    public static function missingFields(array $fields)
    {
        return self::create(
            JsonRpcError::INVALID_REQUEST,
            sprintf('Invalid JSONRPC 2.0 Request. Missing fields %s', implode(', ', $fields))
        );
    }

    public static function invalidVersion($expected, $actual)
    {
        return self::create(
            JsonRpcError::INVALID_REQUEST,
            sprintf('Invalid JSONRPC 2.0 Request. Version mismatch: %s expected, %s given', $expected, $actual)
        );
    }
}
