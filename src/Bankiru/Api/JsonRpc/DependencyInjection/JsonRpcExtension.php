<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection;

use Bankiru\Api\JsonRpc\Serializer\RelationsHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class JsonRpcExtension extends Extension implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('jsonrpc.yml');
    }

    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has('doctrine')) {
            return;
        }

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
