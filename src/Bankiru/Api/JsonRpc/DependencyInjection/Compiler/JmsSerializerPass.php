<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection\Compiler;

use Bankiru\Api\JsonRpc\Listener\SerializedJsonRpcResponseListener;
use Bankiru\Api\Rpc\RpcEvents;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JmsSerializerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('jms_serializer')) {
            return;
        }

        $definition = $container->register('jsonrpc.view.serialize_response', SerializedJsonRpcResponseListener::class);
        $definition->setArguments(
            [
                new Reference('jms_serializer'),
            ]
        );
        $definition->addTag(
            'kernel.event_listener',
            [
                'event'    => RpcEvents::VIEW,
                'method'   => 'onPlainResponse',
                'priority' => -254,
            ]
        );
    }
}
