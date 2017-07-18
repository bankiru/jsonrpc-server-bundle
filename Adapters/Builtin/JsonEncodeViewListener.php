<?php

namespace Bankiru\Api\JsonRpc\Adapters\Builtin;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\ViewEvent;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class JsonEncodeViewListener
{
    public function onPlainResponse(ViewEvent $event)
    {
        $request  = $event->getRequest();
        $response = $event->getResponse();

        // No need to perform JSON-RPC serialization here
        if (!$request instanceof JsonRpcRequestInterface || $request->isNotification()) {
            return;
        }

        // Response is already properly formatted
        if ($response instanceof JsonRpcResponseInterface) {
            return;
        }

        $event->setResponse(
            new JsonRpcResponse(
                $request->getId(),
                $event->getResponse()
            )
        );
    }
}
