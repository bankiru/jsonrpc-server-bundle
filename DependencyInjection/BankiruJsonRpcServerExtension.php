<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection;

use Bankiru\Api\JsonRpc\Listener\ViewListener;
use Bankiru\Api\Rpc\RpcEvents;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;

final class BankiruJsonRpcServerExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('jsonrpc.yml');

        $config = $this->processConfiguration(new Configuration(), $configs);

        $this->configureSecurity($container);
        $this->configureBuiltinAdapter($container);
        $this->configureJmsAdapter($container, $config);

        if ($this->isConfigEnabled($container, $config['view_listener'])) {
            $container->register('jsonrpc_server.response_listener.normalizer', ViewListener::class)
                      ->setPublic(true)
                      ->setArguments([new Reference($config['view_listener']['normalizer'])])
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
    private function configureBuiltinAdapter(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/adapters'));

        $loader->load('builtin.yml');
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $config
     */
    private function configureJmsAdapter(ContainerBuilder $container, array $config)
    {
        if (!in_array(JMSSerializerBundle::class, $container->getParameter('kernel.bundles'), true)) {
            return;
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config/adapters'));
        $loader->load('jms.yml');

        if (!$this->isConfigEnabled($container, $config['adapters']['jms'])) {
            return;
        }

        $container->setParameter('jsonrpc_server.jms.handlers', (array)$config['adapters']['jms']['relation_handlers']);
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureSecurity(ContainerBuilder $container)
    {
        if (in_array(SecurityBundle::class, $container->getParameter('kernel.bundles'), true)) {
            $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
            $loader->load('security.yml');
        }
    }
}
