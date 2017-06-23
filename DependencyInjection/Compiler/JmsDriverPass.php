<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection\Compiler;

use Bankiru\Api\JsonRpc\Adapters\JMS\HandledTypeDriver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JmsDriverPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine')) {
            return;
        }

        $container->register('jms_serializer.driver.relation', HandledTypeDriver::class)
                  ->setArguments(
                      [
                          new Reference('jms_serializer.metadata.doctrine_type_driver'),
                          new Reference('annotation_reader'),
                      ]
                  );

        $container->setAlias('jms_serializer.metadata_driver', 'jms_serializer.driver.relation');

        $container->register('jms_serializer.handler.relation', RelationsHandler::class)
                  ->setArguments([new Reference('doctrine.orm.entity_manager')])
                  ->addTag(
                      'jms_serializer.handler',
                      [
                          'type'      => 'Relation',
                          'direction' => 'serialization',
                          'format'    => 'json',
                          'method'    => 'serializeRelation',
                      ]
                  )
                  ->addTag(
                      'jms_serializer.handler',
                      [
                          'type'      => 'Relation',
                          'direction' => 'deserialization',
                          'format'    => 'json',
                          'method'    => 'deserializeRelation',
                      ]
                  )
                  ->addTag(
                      'jms_serializer.handler',
                      [
                          'type'      => 'Relation<?>',
                          'direction' => 'serialization',
                          'format'    => 'json',
                          'method'    => 'serializeRelation',
                      ]
                  )
                  ->addTag(
                      'jms_serializer.handler',
                      [
                          'type'      => 'Relation<?>',
                          'direction' => 'deserialization',
                          'format'    => 'json',
                          'method'    => 'deserializeRelation',
                      ]
                  );
    }
}
