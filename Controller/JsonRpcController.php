<?php

namespace Bankiru\Api\JsonRpc\Controller;

use Bankiru\Api\JsonRpc\Exception\InvalidRequestException;
use Bankiru\Api\JsonRpc\Exception\RpcMethodNotFoundException;
use Bankiru\Api\JsonRpc\Http\JsonRpcHttpResponse;
use Bankiru\Api\JsonRpc\Specification\JsonRpcRequest;
use Bankiru\Api\JsonRpc\Specification\RichJsonRpcRequest;
use Bankiru\Api\Rpc\Controller\RpcController;
use Bankiru\Api\Rpc\Routing\ControllerResolver\ControllerResolverInterface;
use ScayTrase\Api\Rpc\RpcResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class JsonRpcController extends RpcController
{
    /**
     * JSON-RPC Controller
     *
     * @param Request $request
     *
     * @return JsonRpcHttpResponse
     *
     * @throws BadRequestHttpException
     * @throws RpcMethodNotFoundException
     * @throws InvalidRequestException
     */
    public function jsonRpcAction(Request $request)
    {
        $request->attributes->set('_format', 'json');

        $jsonrpc = json_decode($request->getContent());
        if ((!is_array($jsonrpc) && !is_object($jsonrpc)) || json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Not a valid JSON-RPC request');
        }

        $singleRequest = false;
        if (!is_array($jsonrpc)) {
            $jsonrpc       = [$jsonrpc];
            $singleRequest = true;
        }

        $responses = [];
        foreach ($jsonrpc as $call) {
            if (!$call instanceof \stdClass) {
                throw InvalidRequestException::notAJsonRpc();
            }
            $response = $this->handle($call, $request->get('_route'));
            if (null !== $response) {
                $responses[] = $response;
            }
        }

        if ($singleRequest) {
            $responses = array_shift($responses);
        }

        return new JsonRpcHttpResponse($responses);
    }

    /**
     * @return ControllerResolverInterface
     */
    protected function getResolver()
    {
        return $this->get('jsonrpc_server.controller_resolver');
    }

    /**
     * @param \stdClass $call
     * @param string    $endpoint
     *
     * @return null|RpcResponseInterface Null on notification response
     * @throws \Exception
     */
    private function handle(\stdClass $call, $endpoint)
    {
        $jsonRequest = new RichJsonRpcRequest(JsonRpcRequest::fromStdClass($call));
        $jsonRequest->getAttributes()->set('_endpoint', $endpoint);

        try {
            $jsonResponse = $this->handleSingleRequest($jsonRequest);
        } catch (\Exception $e) {
            $jsonResponse = $this->handleException($e, $jsonRequest);
        }

        if ($jsonRequest->isNotification()) {
            return null;
        }

        return $jsonResponse;
    }
}
