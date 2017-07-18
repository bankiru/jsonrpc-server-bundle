<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection;

use Bankiru\Api\JsonRpc\Adapters\JMS\Compiler\JmsDriverPass;
use Bankiru\Api\JsonRpc\Adapters\JMS\Compiler\RelationHandlerHelper;
use Bankiru\Api\Rpc\RpcEvents;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BankiruJsonRpcServerExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('jsonrpc.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->configureSecurity($container, $config);
        $this->configureBuiltinAdapter($container, $config);
        $this->configureJmsAdapter($container, $config);

        if (!empty($config['view_listener'])) {
            $container->getDefinition($config['view_listener'])
                      ->setPublic(true)
                      ->addTag(
                          'kernel.event_listener',
                          [
                              'event'    => RpcEvents::VIEW,
                              'method'   => 'onPlainResponse',
                              'priority' => -254,
                          ]
                      );
        }
    }

    public function getAlias()
    {
        return 'jsonrpc_server';
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureBuiltinAdapter(ContainerBuilder $container, array $config)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/adapters'));

        $loader->load('builtin.yml');
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureJmsAdapter(ContainerBuilder $container, array $config)
    {
        if (!in_array(JMSSerializerBundle::class, $container->getParameter('kernel.bundles'), true)) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/adapters'));
        $loader->load('jms.yml');


        foreach ($config['adapters']['jms']['relation_handlers'] as $handler => $emid) {
            RelationHandlerHelper::ConfigureRelationHandler($container, $handler, $emid);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureSecurity(ContainerBuilder $container, array $config)
    {
        if (in_array(SecurityBundle::class, $container->getParameter('kernel.bundles'), true)) {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('security.yml');
        }
    }
}
