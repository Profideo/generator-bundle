<?php

namespace Profideo\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Profideo\GeneratorBundle\Generator\BundleGenerator;
use Profideo\GeneratorBundle\Manipulator\KernelManipulator;

/*
 *  Generates bundles defined in configuration that could be a child of another one and enables it in AppKernel.
 */
class GenerateBundlesCommand extends ContainerAwareCommand
{
    /**
     * @see Command
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

        $bundles = $container->getParameter('profideo_generator.bundles');

        if (empty($bundles)) {
            throw new \RuntimeException('There is not bundle registered.');
        }

        foreach ($bundles as $bundle) {
            $bundleName = $bundle['class_prefix'].ucfirst($bundle['name']).'Bundle';
            $namespace = $bundle['base_namespace'].'\\'.ucfirst($bundle['name']).'Bundle';

            $generator = new BundleGenerator($container->get('filesystem'));
            $generator->setSkeletonDirs([__DIR__.'/../Resources/skeleton']);
            $generator->generate($namespace, $bundleName, 'src', null, null, ['parent' => $bundle['parent']]);

            $output->writeln("Generating the bundle '$namespace\\$bundleName'".
                             (!empty($bundle['parent']) ? " as child of '{$bundle['parent']}'" : '').
                             ' : <info>OK</info>');

            $kernelManipulator = new KernelManipulator($container->get('kernel'));
            $kernelManipulator->removeNamespace($bundle['base_namespace']);
            $kernelManipulator->addBundle("$namespace\\$bundleName");

            $output->writeln("Enabling bundle '$namespace\\$bundleName' in AppKernel and disabling others that are".
                             " included in '{$bundle['base_namespace']}' : <info>OK</info>");
        }
    }
}
