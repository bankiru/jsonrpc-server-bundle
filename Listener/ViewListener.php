<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\JsonRpc\Specification\RichJsonRpcRequest;
use Bankiru\Api\Rpc\Event\ViewEvent;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class ViewListener
{
    public function onPlainResponse(ViewEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        // No need to perform JSON-RPC serialization here
        if (!$request instanceof RichJsonRpcRequest || $request->isNotification()) {
            return;
        }

        // Response is already properly formatted
        if ($response instanceof JsonRpcResponseInterface) {
            return;
        }

        $response = new JsonRpcResponse($request->getId(), $response);

        $event->setResponse($response);
    }
}
