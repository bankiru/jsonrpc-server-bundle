<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS\Compiler;

use Bankiru\Api\JsonRpc\Adapters\JMS\RelationsHandler;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RelationHandlerPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getParameter('jsonrpc_server.handlers') as $handler => $emid) {
            $this->configureRelationHandler($container, $handler, $emid);
        }
    }

    private function configureRelationHandler(ContainerBuilder $builder, $handler, $emid)
    {
        if (!$builder->has($emid)) {
            return;
        }

        $builder->register('jms_serializer.handler.relation.' . $handler, RelationsHandler::class)
                ->setArguments([new Reference($emid)])
                ->addTag(
                    'jms_serializer.handler',
                    [
                        'type'      => $handler,
                        'direction' => 'serialization',
                        'format'    => 'json',
                        'method'    => 'serializeRelation',
                    ]
                )
                ->addTag(
                    'jms_serializer.handler',
                    [
                        'type'      => $handler,
                        'direction' => 'deserialization',
                        'format'    => 'json',
                        'method'    => 'deserializeRelation',
                    ]
                )
                ->addTag(
                    'jms_serializer.handler',
                    [
                        'type'      => $handler . '<?>',
                        'direction' => 'serialization',
                        'format'    => 'json',
                        'method'    => 'serializeRelation',
                    ]
                )
                ->addTag(
                    'jms_serializer.handler',
                    [
                        'type'      => $handler . '<?>',
                        'direction' => 'deserialization',
                        'format'    => 'json',
                        'method'    => 'deserializeRelation',
                    ]
                );
    }
}
