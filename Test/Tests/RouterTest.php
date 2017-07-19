<?php

namespace Bankiru\Api\JsonRpc\Test\Tests;

use Bankiru\Api\Rpc\Routing\MethodCollection;
use Bankiru\Api\Rpc\Routing\Router;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\RequestContext;

final class RouterTest extends WebTestCase
{
    public function testHttpRoutes()
    {
        $client  = self::createClient();
        $router  = $client->getContainer()->get('router');
        $context = new RequestContext();
        $context->setMethod('POST');
        $router->setContext($context);
        self::assertNotEmpty($router->match('/test/'));
    }

    public function testRpcRouterCollection()
    {
        $client = self::createClient();

        foreach (['test', 'test_private'] as $endpoint) /** @var MethodCollection $collection */ {
            /** @var Router $router */
            $router = $client->getContainer()->get('rpc_server.endpoint_router.' . $endpoint);
            self::assertNotNull($router);
            $collection = $router->getMethodCollection();
            self::assertNotNull($router);
            self::assertInstanceOf(MethodCollection::class, $collection);

            self::assertTrue($collection->has('sample'));
            self::assertTrue($collection->has('array'));
            self::assertTrue($collection->has('entity'));
            self::assertTrue($collection->has('notification'));
            self::assertTrue($collection->has('custom/method'));
        }
    }
}
