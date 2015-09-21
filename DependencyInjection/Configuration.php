<?php

namespace Profideo\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @codeCoverageIgnore
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('profideo_generator');

        $rootNode
             ->children()
                 ->arrayNode('bundles')
                     ->prototype('array')
                          ->children()
                              ->scalarNode('name')->isRequired()->end()
                              ->scalarNode('base_namespace')->isRequired()->end()
                              ->scalarNode('parent')->defaultNull()->end()
                              ->scalarNode('class_prefix')->defaultNull()->end()
                          ->end()
                     ->end()
                 ->end()
             ->end();

        return $treeBuilder;
    }
}
