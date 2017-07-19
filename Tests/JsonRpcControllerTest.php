<?php

namespace Bankiru\Api\JsonRpc\Tests;

use Bankiru\Api\JsonRpc\Controller\JsonRpcController;
use Bankiru\Api\JsonRpc\Http\JsonRpcHttpResponse;
use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\FilterControllerEvent;
use Bankiru\Api\Rpc\Event\FilterResponseEvent;
use Bankiru\Api\Rpc\Event\FinishRequestEvent;
use Bankiru\Api\Rpc\Event\GetExceptionResponseEvent;
use Bankiru\Api\Rpc\Event\GetResponseEvent;
use Bankiru\Api\Rpc\Routing\ControllerResolver\ControllerResolverInterface;
use Bankiru\Api\Rpc\RpcEvents;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use ScayTrase\Api\JsonRpc\JsonRpcError;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use ScayTrase\Api\Rpc\RpcRequestInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;

final class JsonRpcControllerTest extends TestCase
{
    public function testEmptyBatchRequestHandling()
    {
        $controller = $this->createController();

        $request  = $this->createJsonRequest('/', []);
        $response = $controller->jsonRpcAction($request);

        self::assertInstanceOf(JsonRpcHttpResponse::class, $response);
        self::assertTrue($response->isSuccessful());
        self::assertEquals('[]', $response->getContent());
    }

    public function getInvalidJsonRequests()
    {
        return [
            'empty'        => [null],
            'string'       => ['test'],
            'invalid_json' => [substr(json_encode(['test']), 2)],
        ];
    }

    /**
     * @dataProvider getInvalidJsonRequests
     * @expectedException \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @param $content
     */
    public function testInvalidJsonHandling($content)
    {
        $controller = $this->createController();

        $request = $this->createJsonRequest('/', $content);
        $controller->jsonRpcAction($request);
    }

    public function getInvalidJsonRpcRequests()
    {
        return [
            'invalid version' => [['jsonrpc' => '1.0']],
            'no method'       => [['jsonrpc' => '2.0']],
            'no version'      => [['method' => 'test']],
        ];
    }

    /**
     * @dataProvider getInvalidJsonRpcRequests
     * @expectedException \Bankiru\Api\JsonRpc\Exception\InvalidRequestException
     *
     * @param $content
     */
    public function testInvalidJsonRpcHandling($content)
    {
        $controller = $this->createController();

        $request = $this->createJsonRequest('/', $content);
        $controller->jsonRpcAction($request);
    }

    public function testSingleRequestHandling()
    {
        $controller = $this->createController();
        $request    = $this->createJsonRequest(
            '/',
            [
                'jsonrpc' => '2.0',
                'id'      => 'test',
                'method'  => 'test',
            ]
        );
        $response   = $controller->jsonRpcAction($request);

        self::assertTrue($response->isSuccessful());
        self::assertEquals('{"jsonrpc":"2.0","id":"test","result":{"success":true}}', $response->getContent());
    }

    public function testBatchRequestHandling()
    {
        $controller = $this->createController();
        $request    = $this->createJsonRequest(
            '/',
            [
                [
                    'jsonrpc' => '2.0',
                    'id'      => 'test',
                    'method'  => 'test',
                ],
            ]
        );
        $response   = $controller->jsonRpcAction($request);

        self::assertTrue($response->isSuccessful());
        self::assertEquals('[{"jsonrpc":"2.0","id":"test","result":{"success":true}}]', $response->getContent());
    }

    public function testExceptionHandling()
    {
        $controller = $this->createController();
        $request    = $this->createJsonRequest(
            '/',
            [
                'jsonrpc' => '2.0',
                'id'      => 'test',
                'method'  => 'exception',
            ]
        );
        $response   = $controller->jsonRpcAction($request);

        self::assertTrue($response->isSuccessful());
        self::assertEquals(
            '{"jsonrpc":"2.0","id":"test","error":{"code":-32603,"message":"Failure!","data":null}}',
            $response->getContent()
        );
    }

    public function testExceptionHandlingAmongBatchRequest()
    {
        $controller = $this->createController();
        $request    = $this->createJsonRequest(
            '/',
            [
                [
                    'jsonrpc' => '2.0',
                    'id'      => 'test1',
                    'method'  => 'test',
                ],
                [
                    'jsonrpc' => '2.0',
                    'id'      => 'test2',
                    'method'  => 'exception',
                ],
            ]
        );
        $response   = $controller->jsonRpcAction($request);

        self::assertTrue($response->isSuccessful());
        self::assertEquals(
            '[{"jsonrpc":"2.0","id":"test1","result":{"success":true}},{"jsonrpc":"2.0","id":"test2","error":{"code":-32603,"message":"Failure!","data":null}}]',
            $response->getContent()
        );
    }

    private function createJsonRequest($uri, $content)
    {
        return Request::create($uri, 'POST', [], [], [], [], json_encode($content));
    }

    private function getContainerMock()
    {
        $mock     = $this->prophesize(ContainerInterface::class);
        $kernel   = $this->prophesize(KernelInterface::class);
        $resolver = $this->prophesize(ControllerResolverInterface::class);
        $resolver->getController(Argument::type(RpcRequestInterface::class))->willReturn(
            function (JsonRpcRequestInterface $request) {
                if ($request->getMethod() === 'exception') {
                    throw new \LogicException('Failure!');
                }

                return new JsonRpcResponse($request->getId(), ['success' => true]);
            }
        );
        $resolver->getArguments(Argument::type(RpcRequestInterface::class), Argument::any())->will(
            function (array $args) {
                return [
                    $args[0],
                ];
            }
        );

        $evm = $this->prophesize(EventDispatcherInterface::class);
        $evm->dispatch(Argument::exact(RpcEvents::EXCEPTION), Argument::type(GetExceptionResponseEvent::class))
            ->will(
                function ($args) {
                    /** @var GetExceptionResponseEvent $event */
                    $event = $args[1];

                    /** @var JsonRpcRequestInterface $request */
                    $request = $event->getRequest();

                    $event->setResponse(
                        new JsonRpcResponse(
                            $request->getId(),
                            null, new JsonRpcError(
                                JsonRpcError::INTERNAL_ERROR,
                                $event->getException()->getMessage()
                            )
                        )
                    );
                }
            );
        $evm->dispatch(Argument::exact(RpcEvents::FINISH_REQUEST), Argument::type(FinishRequestEvent::class))
            ->willReturn(null);
        $evm->dispatch(Argument::exact(RpcEvents::CONTROLLER), Argument::type(FilterControllerEvent::class))
            ->willReturn(null);
        $evm->dispatch(Argument::exact(RpcEvents::REQUEST), Argument::type(GetResponseEvent::class))->willReturn(null);
        $evm->dispatch(Argument::exact(RpcEvents::RESPONSE), Argument::type(FilterResponseEvent::class))
            ->willReturn(null);

        $mock->get(Argument::exact('jsonrpc_server.controller_resolver'))->willReturn($resolver->reveal());
        $mock->get(Argument::exact('event_dispatcher'))->willReturn($evm->reveal());
        $mock->get(Argument::exact('kernel'))->willReturn($kernel->reveal());

        return $mock->reveal();
    }

    private function createController()
    {
        $controller = new JsonRpcController();
        $controller->setContainer($this->getContainerMock());

        return $controller;
    }
}
