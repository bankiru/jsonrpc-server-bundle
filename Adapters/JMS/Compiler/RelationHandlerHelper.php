<?php

namespace Bankiru\Api\JsonRpc\Adapters\JMS\Compiler;

use Bankiru\Api\JsonRpc\Adapters\JMS\RelationsHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

final class RelationHandlerHelper
{
    public static function configureRelationHandler(ContainerBuilder $builder, $handler, $emid)
    {
        if (!$builder->has($emid)) {
            return;
        }

        $builder->register('jms_serializer.handler.relation', RelationsHandler::class)
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
