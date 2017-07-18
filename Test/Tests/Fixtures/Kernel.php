<?php

namespace Bankiru\Api\JsonRpc\Test\Tests\Fixtures;

use Bankiru\Api\JsonRpc\BankiruJsonRpcServerBundle;
use Bankiru\Api\JsonRpc\Test\JsonRpcTestBundle;
use Bankiru\Api\Rpc\BankiruRpcServerBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use JMS\SerializerBundle\JMSSerializerBundle;
use Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Tests\Fixtures\KernelForTest;

class Kernel extends KernelForTest
{
    public function registerBundles()
    {
        $bundles = [
            new FrameworkBundle(),
            new SecurityBundle(),
            new SensioFrameworkExtraBundle(),
            new BankiruRpcServerBundle(),
            new BankiruJsonRpcServerBundle(),
            new JsonRpcTestBundle(),
        ];

        if (false !== getenv('JMS_BUNDLE')) {
            $bundles[] = new JMSSerializerBundle();
        }

        if (false !== getenv('DOCTRINE_BUNDLE')) {
            $bundles[] = new DoctrineBundle();
        }

        return $bundles;
    }

    public function getCacheDir()
    {
        return __DIR__ . '/../../../build/cache/';
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../../build/log/';
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
