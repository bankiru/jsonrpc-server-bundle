<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 12.02.2016
 * Time: 12:10
 */

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
        $response = $event->getResponse();
        $request  = $event->getRequest();

        if (!$request instanceof JsonRpcRequestInterface) {
            return;
        }

        if ($response === null) {
            if ($request->isNotification()) {
                $event->setResponse(new JsonRpcResponse());

                return;
            }

            throw new \RuntimeException('Response returned is null but request was not notification');
        }
    }
}
