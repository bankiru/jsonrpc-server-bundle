<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;

final class SampleControllerTest extends JsonRpcTestCase
{
    public function testBatchRequest()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/',
            [
                [
                    'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                    'method'  => 'sample',
                    'id'      => 'test',
                    'params'  => [
                        'param1' => 'value1',
                        'param2' => 100500,
                    ],
                ],
                [
                    //notification - no id
                    'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                    'method'  => 'notification',
                    'param'   => [
                        'notification' => 'message',
                    ],
                ],
            ]
        );

        self::assertTrue($response->isSuccessful(), $response->getContent());
    }

    public function testArrayRequest()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/',
            [
                'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                'method'  => 'array',
                'id'      => 'test',
                'params'  => [
                    'payload' => 'my-payload',
                ],
            ]
        );

        self::assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent());
        self::assertInstanceOf(\stdClass::class, $content);

        $jsonResponse = new SyncResponse($content);
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content));
        self::assertTrue(isset($jsonResponse->getBody()[0]->{'sample_payload'}), json_encode($content, JSON_PRETTY_PRINT));
        self::assertEquals('my-payload', $jsonResponse->getBody()[0]->{'sample_payload'}, json_encode($content, JSON_PRETTY_PRINT));
    }
}
