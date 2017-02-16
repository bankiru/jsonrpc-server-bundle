<?php

namespace Bankiru\Api\JsonRpc\Test\DependencyInjection;

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
        foreach ($container->getExtensions() as $name => $extension) {
            switch ($name) {
                case 'rpc':
                    $container->prependExtensionConfig(
                        $name,
                        [
                            'router' => [
                                'endpoints' => [
                                    'test'         => [
                                        'path'      => '/test/',
                                        'resources' => '@JsonRpcTestBundle/Resources/config/jsonrpc_routes.yml',
                                        'defaults'  => [
                                            '_controller' => 'JsonRpcBundle:JsonRpc:jsonRpc',
                                            '_format'     => 'json',
                                        ],
                                    ],
                                    'test_private' => [
                                        'path'      => '/test/private/',
                                        'defaults'  => [
                                            '_controller' => 'JsonRpcBundle:JsonRpc:jsonRpc',
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
                    break;
            }
        }
    }
}
