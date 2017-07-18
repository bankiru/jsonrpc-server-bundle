<?php

namespace Bankiru\Api\JsonRpc\Test\DependencyInjection;

use JMS\SerializerBundle\JMSSerializerBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class JsonRpcTestExtension extends Extension implements PrependExtensionInterface
{
    /** {@inheritdoc} */
    public function load(array $configs, ContainerBuilder $container)
    {
    }

    /**
     * Allow an extension to prepend the extension configurations.
     *
     * @param ContainerBuilder $container
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig(
            'rpc',
            [
                'router' => [
                    'endpoints' => [
                        'test'         => [
                            'path'      => '/test/',
                            'resources' => '@JsonRpcTestBundle/Resources/config/jsonrpc_routes.yml',
                            'defaults'  => [
                                '_controller' => 'BankiruJsonRpcServerBundle:JsonRpc:jsonRpc',
                                '_format'     => 'json',
                            ],
                        ],
                        'test_private' => [
                            'path'      => '/test/private/',
                            'defaults'  => [
                                '_controller' => 'BankiruJsonRpcServerBundle:JsonRpc:jsonRpc',
                                '_format'     => 'json',
                            ],
                            'resources' => [
                                '@JsonRpcTestBundle/Resources/config/jsonrpc_routes.yml',
                                '@JsonRpcTestBundle/Resources/config/jsonrpc_private.yml',
                            ],
                        ],
                    ],
                ],
            ]
        );

        if (in_array(JMSSerializerBundle::class, $container->getParameter('kernel.bundles'), true)) {
            $container->prependExtensionConfig(
                'jsonrpc_server',
                ['view_listener' => 'jsonrpc.jms_adapter.view_listener']
            );
        }
    }
}
