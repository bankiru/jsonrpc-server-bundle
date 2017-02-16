<?php

namespace Bankiru\Api\JsonRpc\Test\Tests\Fixtures;

use Bankiru\Api\JsonRpc\JsonRpcBundle;
use Bankiru\Api\JsonRpc\Test\JsonRpcTestBundle;
use Bankiru\Api\Rpc\RpcBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Nelmio\ApiDocBundle\NelmioApiDocBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;

class Kernel extends KernelForTest
{
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
            new JMSSerializerBundle(),
            new NelmioApiDocBundle(),
            new TwigBundle(),
            new RpcBundle(),
            new JsonRpcBundle(),
            new JsonRpcTestBundle(),
        ];
    }

    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/jsonrpc-test/cache';
    }

    public function getLogDir()
    {
        return sys_get_temp_dir() . '/jsonrpc-test/log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');
    }

    public function getEnvironment()
    {
        return 'test';
    }
}
