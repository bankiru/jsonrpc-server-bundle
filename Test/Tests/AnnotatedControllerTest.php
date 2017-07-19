<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\SyncResponse;

final class AnnotatedControllerTest extends JsonRpcTestCase
{
    public function testNestedContext()
    {
        $client   = self::createClient();
        $response = $this->sendRequest(
            $client,
            '/test/private/',
            [
                'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
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
        self::assertTrue($jsonResponse->isSuccessful(), json_encode($content, JSON_PRETTY_PRINT));
    }

    /**
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessage Not a valid JSON-RPC request
     */
    public function testEmptyRequest()
    {
        $client = self::createClient();
        $this->sendRequest(
            $client,
            '/test/private/',
            null
        );
    }
}
