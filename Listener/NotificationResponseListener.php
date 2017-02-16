<?php

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\ViewEvent;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

final class NotificationResponseListener
{
    /**
     * Sets temporary response to bypass view checking
     *
     * @param ViewEvent $event
     *
     * @throws \RuntimeException
     */
    public function onNullResponse(ViewEvent $event)
    {
        $request  = $event->getRequest();

        if (!$request instanceof JsonRpcRequestInterface) {
            return;
        }

        // bypass view event
        if ($request->isNotification()) {
            $event->setResponse(new JsonRpcResponse());
        }
    }
}
