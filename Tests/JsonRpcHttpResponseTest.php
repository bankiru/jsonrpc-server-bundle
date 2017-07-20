<?php

namespace Bankiru\Api\JsonRpc\Tests;

use Bankiru\Api\JsonRpc\Http\JsonRpcHttpResponse;
use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use PHPUnit\Framework\TestCase;
use ScayTrase\Api\JsonRpc\JsonRpcError;

final class JsonRpcHttpResponseTest extends TestCase
{
    public function getResponseVariants()
    {
        $success = new JsonRpcResponse('single', (object)['success' => true], null);

        $error = new JsonRpcResponse(
            'single',
            null,
            new JsonRpcError(JsonRpcError::METHOD_NOT_FOUND, 'Invalid method')
        );

        return [
            'empty'          => [null],
            'empty array'    => [[]],
            'single success' => [clone $success],
            'single failure' => [clone $error],
            'mixed array'    => [[clone $success, clone $error]],
        ];
    }

    /**
     * @dataProvider getResponseVariants
     *
     * @param $responses
     */
    public function testResponseProvidesCorrectHeaders($responses)
    {
        $response = new JsonRpcHttpResponse($responses);
        self::assertTrue($response->isSuccessful());
        self::assertTrue($response->headers->has('Content-Type'));
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
    }
}
