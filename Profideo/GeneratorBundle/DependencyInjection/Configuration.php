<?php

namespace Profideo\GeneratorBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

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
                ->scalarNode('base_namespace')
                    ->isRequired()
                ->end()
                ->scalarNode('parent_bundle')
                    ->defaultNull()
                ->end()
                ->scalarNode('class_prefix')
                    ->defaultNull()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
