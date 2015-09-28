<?php

namespace Profideo\GeneratorBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profideo\GeneratorBundle\Generator\BundleGenerator;
use Profideo\GeneratorBundle\Manipulator\KernelManipulator;
use Sensio\Bundle\GeneratorBundle\Command\GeneratorCommand;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Generates bundles defined in configuration and enables it in AppKernel.
 */
class GenerateBundlesCommand extends GeneratorCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('profideo:generate-bundles');
    }

    /**
     * @throws \RuntimeException When bundle can't be executed or not bundle is registered
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();

        $bundles = $container->getParameter('profideo.generator_bundle.bundles');

        if (empty($bundles)) {
            throw new \RuntimeException('There is not bundle registered.');
        }

        foreach ($bundles as $bundle) {
            $bundleName = $bundle['class_prefix'].ucfirst($bundle['name']).'Bundle';
            $namespace = $bundle['base_namespace'].'\\'.ucfirst($bundle['name']).'Bundle';

            $generator = $this->getGenerator();
            $generator->setSkeletonDirs([__DIR__.'/../Resources/skeleton']);
            $generator->generate($namespace, $bundleName, 'src', null, null, ['parent' => $bundle['parent']]);

            $output->writeln("Generating the bundle '$namespace\\$bundleName'".
                             (!empty($bundle['parent']) ? " as child of '{$bundle['parent']}'" : '').
                             ' : <info>OK</info>');

            $this->updateKernel($container->get('kernel'), $bundle['base_namespace'], "$namespace\\$bundleName");

            $output->writeln("Enabling bundle '$namespace\\$bundleName' in AppKernel and disabling others that are".
                             " included in '{$bundle['base_namespace']}' : <info>OK</info>");
        }
    }

    /**
     * Removes all bundles in $baseNamespace and add bundle $bundle.
     *
     * @param KernelInterface $kernel
     * @param string          $baseNamespace
     * @param string          $bundle
     */
    protected function updateKernel(KernelInterface $kernel, $baseNamespace, $bundle)
    {
        $kernelManipulator = new KernelManipulator($kernel);
        $kernelManipulator->removeNamespace($baseNamespace);
        $kernelManipulator->addBundle($bundle);
    }

    /**
     * Returns an instance of BundleGenerator.
     *
     * @return BundleGenerator
     */
    protected function createGenerator()
    {
        return new BundleGenerator($this->getContainer()->get('filesystem'));
    }
}
