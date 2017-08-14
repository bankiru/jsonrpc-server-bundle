<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\BrowserKit\JsonRpcClient;
use Bankiru\Api\JsonRpc\Test\Kernel\TestKernel;
use ScayTrase\Api\IdGenerator\UuidGenerator;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;
use ScayTrase\Api\Rpc\RpcRequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\Client;

abstract class JsonRpcTestCase extends WebTestCase
{
    protected static function getKernelClass()
    {
        return TestKernel::class;
    }

    /**
     * @param        $endpoint
     * @param Client $client
     * @param        $requests
     *
     * @return null|\Symfony\Component\HttpFoundation\Response
     */
    protected function sendRequest(Client $client = null, $endpoint, $requests)
    {
        if (null === $client) {
            $client = static::createClient();
        }

        $client->request('POST', $endpoint, [], [], [], json_encode($requests));

        return $client->getResponse();
    }

    /**
     * @param RpcRequestInterface $request
     * @param string              $endpoint
     * @param Client|null         $client
     *
     * @return JsonRpcResponseInterface
     */
    protected function sendJsonRpcRequest(RpcRequestInterface $request, $endpoint, Client $client = null)
    {
        if (null === $client) {
            $client = static::createClient();
        }

        $jsonRpcClient = new JsonRpcClient($client, $endpoint, new UuidGenerator());

        return $jsonRpcClient->invoke($request)->getResponse($request);
    }
}
