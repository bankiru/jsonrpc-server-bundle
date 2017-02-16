<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use ScayTrase\Api\JsonRpc\SyncResponse;
use Symfony\Component\HttpFoundation\Response;

class ExceptionHandlingTest extends JsonRpcTestCase
{
    public function testBatchWithFailingMethod()
    {
        $client = self::createClient();

        $response = $this->sendRequest(
            $client,
            '/test/',
            [
                [
                    'jsonrpc' => JsonRpcBundle::VERSION,
                    'method'  => 'sample',
                    'id'      => 'test',
                    'params'  => [
                        'param1' => 'value1',
                        'param2' => 100500,
                    ],
                ],
                [
                    'jsonrpc' => JsonRpcBundle::VERSION,
                    'method'  => 'exception',
                    'id'      => 'test2',
                    'params'  => [
                        'param1' => 'value1',
                        'param2' => 100500,
                    ],
                ],
                [
                    //notification - no id
                    'jsonrpc' => JsonRpcBundle::VERSION,
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
                    'jsonrpc' => JsonRpcBundle::VERSION,
                    'id'      => 'test',
                    'params'  => [],
                ],
            ],
            'No id'            => [
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
     * @dataProvider getInvalidRequests
     *
     * @param array $request
     */
    public function testNoMethodResultsInHttp400Error(array $request)
    {
        $client = self::createClient();

        $response = $this->sendRequest(
            $client,
            '/test/',
            $request
        );

        self::assertFalse($response->isSuccessful());
        self::assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
    }
}
