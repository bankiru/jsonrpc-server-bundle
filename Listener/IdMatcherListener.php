<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\FilterResponseEvent;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class IdMatcherListener
{
    /**
     * Rewrites response to match request id if it was a JSON-RPC response
     *
     * @param FilterResponseEvent $event
     */
    public function onFilterResponse(FilterResponseEvent $event)
    {
        /** @var JsonRpcRequestInterface $request */
        $request = $event->getRequest();
        if (!($request instanceof JsonRpcRequestInterface)) {
            return;
        }

        // no need to rewrite id for notification response
        if ($request->isNotification()) {
            return;
        }

        $response = $event->getResponse();

        // response id is set and correct
        if ($response instanceof JsonRpcResponseInterface && $response->getId() === $request->getId()) {
            return;
        }

        $response = new JsonRpcResponse($request->getId(), $response->getBody(), $response->getError());
        $event->setResponse($response);
    }
}
