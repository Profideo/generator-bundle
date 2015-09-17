<?php

namespace Profideo\GeneratorBundle\DependencyInjection;

use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ProfideoGeneratorExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('profideo_generator.base_namespace', $config['base_namespace']);
        $container->setParameter('profideo_generator.parent_bundle', $config['parent_bundle']);
        $container->setParameter('profideo_generator.class_prefix', $config['class_prefix']);
    }
}