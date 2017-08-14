<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection\Compiler;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

final class SymfonyAdapterConfigurationPass implements CompilerPassInterface
{
    /** {@inheritdoc} */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('serializer')) {
            return;
        }

        if ($this->hasSymfonySerializer($container)) {
            $this->configureSymfonyAdapter($container);
        }
    }

    /**
     * @param ContainerBuilder $container
     */
    private function configureSymfonyAdapter(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../Resources/config/adapters'));

        $loader->load('symfony.yml');
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return bool
     */
    private function hasSymfonySerializer(ContainerBuilder $container)
    {
        $interfaces = class_implements(
            $container->getParameterBag()->resolveValue($container->getDefinition('serializer')->getClass())
        );

        return in_array(NormalizerInterface::class, $interfaces, true);
    }
}
