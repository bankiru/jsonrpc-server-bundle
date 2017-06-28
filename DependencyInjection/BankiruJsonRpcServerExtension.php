<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection;

use Bankiru\Api\JsonRpc\Adapters\JMS\Compiler\JmsDriverPass;
use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class BankiruJsonRpcServerExtension extends Extension
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
        $bundles = $container->getParameter('kernel.bundles');

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('jsonrpc.yml');

        if (in_array(SecurityBundle::class, $bundles, true)) {
            $loader->load('security.yml');
        }

        if (in_array(JMSSerializerBundle::class, $bundles, true)) {
            $loader->load('jms.yml');
            $container->addCompilerPass(new JmsDriverPass(), PassConfig::TYPE_BEFORE_REMOVING);
        }
    }
}
