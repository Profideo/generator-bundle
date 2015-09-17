<?php

namespace Profideo\GeneratorBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Profideo\GeneratorBundle\Generator\BundleGenerator;
use Profideo\GeneratorBundle\Manipulator\KernelManipulator;

/*
 *  Generates a bundle could be a child of another one and enables it in AppKernel.
 */
class GenerateBundleCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'The name of the broadcast client.'),
            ))
            ->setName('profideo:generate-bundle');
    }

    /**
     * @throws \RuntimeException When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        $containter = $this->getContainer();
        $baseNamespace = $containter->getParameter('profideo_generator.base_namespace');
        $parentBundle = $containter->getParameter('profideo_generator.parent_bundle');
        $bundle = $containter->getParameter('profideo_generator.class_prefix').ucfirst($name).'Bundle';
        $namespace = "$baseNamespace\\".ucfirst($name).'Bundle';

        $generator = new BundleGenerator($this->getContainer()->get('filesystem'));
        $generator->setSkeletonDirs([__DIR__.'/../Resources/skeleton']);
        
        $generator->generate($namespace, $bundle, 'src', null, null, ['parent_bundle' => $parentBundle]);
        $output->writeln("Generating the bundle '$namespace\\$bundle'" . (null !== $parentBundle ? " as child of '$parentBundle'" : ''). " : <info>OK</info>");

        $kernelManipulator = new KernelManipulator($this->getContainer()->get('kernel'));
        $kernelManipulator->removeNamespace($baseNamespace);
        $kernelManipulator->addBundle("$namespace\\$bundle");
        $output->writeln("Enabling bundle '$namespace\\$bundle' in AppKernel and disabling others one that are included in '$baseNamespace' : <info>OK</info>");
    }
}
