<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use ScayTrase\Api\JsonRpc\SyncResponse;

final class ExceptionHandlingTest extends JsonRpcTestCase
{
    public function testBatchWithFailingMethod()
    {
        $client = self::createClient();

        $response = $this->sendRequest(
            $client,
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
                    'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                    'method'  => 'exception',
                    'id'      => 'test2',
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
        $body = json_decode($response->getContent());
        self::assertTrue(is_array($body));
        self::assertCount(2, $body, json_encode($body, JSON_PRETTY_PRINT));

        /** @var JsonRpcResponseInterface[] $jsonResponses */
        $jsonResponses = [];
        foreach ($body as $responseBody) {
            $jsonResponses[] = new SyncResponse($responseBody);
        }

        self::assertTrue($jsonResponses[0]->isSuccessful());
        self::assertFalse($jsonResponses[1]->isSuccessful());
        self::assertEquals(JsonRpcError::INTERNAL_ERROR, $jsonResponses[1]->getError()->getCode());
        self::assertEquals('This is the malfunction controller', $jsonResponses[1]->getError()->getMessage());
    }

    public function getInvalidRequests()
    {
        return [
            'No method'        => [
                [
                    'jsonrpc' => BankiruJsonRpcServerBundle::JSONRPC_VERSION,
                    'id'      => 'test',
                    'params'  => [],
                ],
            ],
            'No version'       => [
                [
                    'id'     => 'test',
                    'method' => 'sample',
                    'params' => [],
                ],
            ],
            'No id, no method' => [
                [
                    'id'     => 'test',
                    'params' => [],
                ],
            ],
        ];
    }

    /**
     * @dataProvider             getInvalidRequests
     *
     * @param array $request
     *
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @expectedExceptionMessageRegExp |^Invalid JSONRPC 2.0 Request. Missing fields .+$|
     */
    public function testInvalidJsonRpcResultsIn400Response(array $request)
    {
        $client = self::createClient();

        $this->sendRequest(
            $client,
            '/test/',
            $request
        );
    }
}
