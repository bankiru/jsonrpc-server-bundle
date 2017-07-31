<?php

namespace Bankiru\Api\JsonRpc\Test\Kernel;

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

final class TestKernel extends KernelForTest
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
        return __DIR__ . '/../../build/' . $this->getName() . '/cache';
    }

    public function getLogDir()
    {
        return __DIR__ . '/../../build/' . $this->getName() . '/log';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__ . '/config.yml');

        if (false !== getenv('JMS_BUNDLE')) {
            $loader->load(__DIR__ . '/config_jms.yml');
        }

        if (false !== getenv('SYMFONY_SERIALIZER')) {
            $loader->load(__DIR__ . '/config_symfony.yml');
        }

        if (false !== getenv('DOCTRINE_BUNDLE')) {
            $loader->load(__DIR__ . '/config_doctrine.yml');
        }
    }

    public function getName()
    {
        $name = 'jsorpc_test';

        if (false !== getenv('JMS_BUNDLE')) {
            $name .= '_jms';
        }

        if (false !== getenv('SYMFONY_SERIALIZER')) {
            $name .= '_symfony';
        }

        if (false !== getenv('DOCTRINE_BUNDLE')) {
            $name .= '_doctrine';
        }

        return $name;
    }

    public function getEnvironment()
    {
        return 'test';
    }
}
