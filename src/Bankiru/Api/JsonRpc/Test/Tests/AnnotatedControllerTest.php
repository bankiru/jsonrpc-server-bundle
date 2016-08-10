<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;
use Symfony\Component\HttpFoundation\Response;

class AnnotatedControllerTest extends JsonRpcTestCase
{
    public function testNestedContext()
    {
        $client   = self::createClient();
        $response = $this->sendRequest(
            $client,
            '/test/private/',
            [
                'jsonrpc' => JsonRpcBundle::VERSION,
                'method'  => 'prefix/annotation/sub',
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
        self::assertTrue($jsonResponse->isSuccessful());
    }

    public function testEmptyRequest()
    {
        $client   = self::createClient();
        $response = $this->sendRequest(
            $client,
            '/test/private/',
            null
        );

        self::assertFalse($response->isSuccessful());
        $content = $response->getContent();
        json_decode($content, true);
        self::assertEquals(JSON_ERROR_NONE, json_last_error());
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
