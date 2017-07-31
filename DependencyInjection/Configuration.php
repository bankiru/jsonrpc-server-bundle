<?php

namespace Bankiru\Api\JsonRpc\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /** {@inheritdoc} */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root    = $builder->root('jsonrpc_server');

        $viewListener = $root
            ->children()
            ->arrayNode('view_listener')
            ->canBeEnabled();

        $viewListener
            ->children()
            ->scalarNode('normalizer')
            ->info('View listener normalizer service ID')
            ->defaultValue('jsonrpc_server.builtin_adapter.normalizer');

        $adapters = $root->children()->arrayNode('adapters');
        $this->configureJms($adapters);

        return $builder;
    }

    /**
     * @param ArrayNodeDefinition $adapters
     */
    private function configureJms(ArrayNodeDefinition $adapters)
    {
        $jms = $adapters->children()->arrayNode('jms');

        $jms->canBeEnabled();

        $jms->children()
            ->arrayNode('relation_handlers')
            ->fixXmlConfig('relation_handler')
            ->useAttributeAsKey('handler')
            ->prototype('scalar')
            ->info(
                'Key: Relation handler name (i.e. "Relation"), Value: service ID for relation handler entity manager'
            )
            ->isRequired();
    }
}
