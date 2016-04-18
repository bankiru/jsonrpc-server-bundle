<?php
/**
 * Created by PhpStorm.
 * User: batanov.pavel
 * Date: 12.02.2016
 * Time: 14:01
 */

namespace Bankiru\Api\JsonRpc\Listener;

use Bankiru\Api\JsonRpc\Specification\JsonRpcResponse;
use Bankiru\Api\Rpc\Event\ViewEvent;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use ScayTrase\Api\JsonRpc\JsonRpcRequestInterface;
use ScayTrase\Api\JsonRpc\JsonRpcResponseInterface;

final class SerializedJsonRpcResponseListener
{
    /** @var  Serializer */
    private $serializer;

    /**
     * SerializedJsonRpcResponseListener constructor.
     *
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer) { $this->serializer = $serializer; }

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

        $response = new JsonRpcResponse(
            $request->getId(),
            json_decode(
                $this->serializer->serialize(
                    $event->getResponse(),
                    'json',
                    $this->createSerializerContext($event)
                )
            )
        );
        $event->setResponse($response);

    }

    /**
     * @param ViewEvent $event
     *
     * @return SerializationContext
     * @throws \LogicException
     */
    private function createSerializerContext(ViewEvent $event)
    {
        $context = SerializationContext::create();
        $context->setSerializeNull(true);
        $attributes = $event->getRequest()->getAttributes();
        $defaults   = $attributes->get('_with_default_context', true);

        if (!$defaults && (false === $attributes->get('_context', false))) {
            throw new \LogicException(
                'Could not perform object serialization as no default context allowed and no custom set'
            );
        }

        $groups = [];
        if ($defaults) {
            $groups[] = 'Default';
        }
        if (false !== $attributes->get('_context', false)) {
            foreach ((array)$attributes->get('_context') as $group) {
                $groups[] = $group;
            }
        }

        $context->setGroups($groups);

        return $context;
    }
}
