<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 11.02.2016
 * Time: 15:24
 */

namespace Bankiru\Api\JsonRpc\Controller;

use Bankiru\Api\JsonRpc\Exception\RpcMethodNotFoundException;
use Bankiru\Api\JsonRpc\Http\JsonRpcHttpResponse;
use Bankiru\Api\JsonRpc\Specification\JsonRpcRequest;
use Bankiru\Api\JsonRpc\Specification\RichJsonRpcRequest;
use Bankiru\Api\Rpc\Controller\RpcController;
use Bankiru\Api\Rpc\Routing\ControllerResolver\ControllerResolverInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use ScayTrase\Api\Rpc\RpcResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class JsonRpcController extends RpcController
{

    /**
     * JSON-RPC Controller
     *
     * @ApiDoc(
     *     resource="HTTP JSON-RPC Endpoints",
     *     description ="HTTP JSON-RPC Endpoint",
     *     requirements ={
     *      {
     *          "name"="jsonrpc",
     *          "dataType"="string",
     *          "requirement"="2.0",
     *          "description"="JSON-RPC version constant"
     *      }
     *     },
     *     parameters={
     *        {"name"="jsonrpc", "dataType"="string", "required"=true, "description"="protocol version"},
     *        {"name"="id", "dataType"="string", "required"=false, "description"="request id (omit for notification)"},
     *        {"name"="method", "dataType"="string", "required"=true, "description"="rpc method"},
     *        {"name"="params", "dataType"="mixed|array|string|object", "required"=false, "description"="method
     *        params"},
     *     }
     * )
     *
     *
     * @param Request $request
     *
     * @return JsonRpcHttpResponse
     * @throws RpcMethodNotFoundException
     */
    public function jsonRpcAction(Request $request)
    {
        $request->attributes->set('_format', 'json');

        $jsonrpc = json_decode($request->getContent());
        if (null === $jsonrpc || json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestHttpException('Not an valid json request');
        }

        $singleRequest = false;
        if (!is_array($jsonrpc)) {
            $jsonrpc       = [$jsonrpc];
            $singleRequest = true;
        }

        $responses = [];
        foreach ($jsonrpc as $call) {
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

    /**
     * @return ControllerResolverInterface
     */
    protected function getResolver()
    {
        return $this->get('jsonrpc.controller_resolver');
    }
}
