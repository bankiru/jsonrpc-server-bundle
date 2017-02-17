<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Not an valid JSON request
     */
    public function testEmptyRequest()
    {
        $client   = self::createClient();
        $this->sendRequest(
            $client,
            '/test/private/',
            null
        );
    }
}
