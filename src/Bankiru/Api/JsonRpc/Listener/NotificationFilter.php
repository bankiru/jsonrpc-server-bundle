<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 16.05.2016
 * Time: 12:16
 */

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\Rpc\Event\FilterResponseEvent;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;

final class NotificationFilter
{
    /**
     * Unsets response if it was a notification
     *
     * @param FilterResponseEvent $event
     */
    public function filterNotificationResponse(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!($request instanceof JsonRpcRequestInterface)) {
            return;
        }

        if ($request->isNotification()) {
            $event->setResponse(null);
        }
    }
}
