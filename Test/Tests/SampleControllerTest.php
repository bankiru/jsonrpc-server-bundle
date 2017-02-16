<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;

class SampleControllerTest extends JsonRpcTestCase
{
    public function testBatchRequest()
    {
        $response = $this->sendRequest(
            self::createClient(),
            '/test/',
            [
                [
                    'jsonrpc' => JsonRpcBundle::VERSION,
                    'method' => 'sample',
                    'id' => 'test',
                    'params' => [
                        'param1' => 'value1',
                        'param2' => 100500,
                    ],
                ],
                [
                    //notification - no id
                    'jsonrpc' => JsonRpcBundle::VERSION,
                    'method' => 'notification',
                    'param' => [
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
                'jsonrpc' => JsonRpcBundle::VERSION,
                'method' => 'array',
                'id' => 'test',
                'params' => [
                    'payload' => 'my-payload',
                ],
            ]
        );

        self::assertTrue($response->isSuccessful());
        $content = json_decode($response->getContent());
        self::assertInstanceOf(\stdClass::class, $content);

        $jsonResponse = new SyncResponse($content);
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content));
        self::assertTrue(isset($jsonResponse->getBody()[0]->{'sample_payload'}));
        self::assertEquals('my-payload', $jsonResponse->getBody()[0]->{'sample_payload'});
    }
}
