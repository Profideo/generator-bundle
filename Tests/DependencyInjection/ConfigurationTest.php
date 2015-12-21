<?php

namespace Profideo\GeneratorBundle\Tests\DependencyInjection;

use Symfony\Component\Console\Tester\CommandTester;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Profideo\GeneratorBundle\Command\GenerateBundleCommand;

class ConfigurationTest extends GenerateCommandTest
{
    /**
     * @dataProvider getGenerateBundleCommandData
     */
    public function testGenerateBundleCommand($configs, $expected)
    {
        $container = $this->getContainer();
        $container->setParameter('profideo.generator_bundle', $configs);

        list($namespace, $bundle, $dir, $format, $structure, $parameters) = $expected;

        $generator = $this
            ->getMockBuilder('Profideo\GeneratorBundle\Generator\BundleGenerator')
            ->disableOriginalConstructor()
            ->setMethods(array('generate'))
            ->getMock()
        ;
        $generator
            ->expects($this->once())
            ->method('generate')
            ->with($namespace, $bundle, $dir, $format, $structure, $parameters)
        ;

        $command = $this
            ->getMockBuilder('Profideo\GeneratorBundle\Command\GenerateBundleCommand')
            ->setMethods(['updateKernel'])
            ->getMock()
        ;

        $command->setGenerator($generator);
        $command->setContainer($container);

        (new CommandTester($command))->execute([]);
    }

    public function getGenerateBundleCommandData()
    {
        return [
            [
                [
                    'name' => 'bundleTest',
                    'base_namespace' => 'TestNameSpace',
                    'parent' => null,
                    'class_prefix' => 'PrefixTest',
                ],
                [
                    'TestNameSpace\BundleTestBundle',
                    'PrefixTestBundleTestBundle',
                    'src',
                    null,
                    null,
                    ['parent' => null],
                ],
            ],
            [
                [
                    'name' => 'bundleTest',
                    'base_namespace' => 'TestNameSpace',
                    'parent' => 'ParentBundleTest',
                    'class_prefix' => null,
                ],
                [
                    'TestNameSpace\BundleTestBundle',
                    'BundleTestBundle',
                    'src',
                    null,
                    null,
                    ['parent' => 'ParentBundleTest'],
                ],
            ],
            [
                [
                    'name' => 'bundleTest',
                    'base_namespace' => 'TestNameSpace',
                    'parent' => 'ParentBundleTest',
                    'class_prefix' => 'PrefixTest',
                ],
                [
                    'TestNameSpace\BundleTestBundle',
                    'PrefixTestBundleTestBundle',
                    'src',
                    null,
                    null,
                    ['parent' => 'ParentBundleTest'],
                ],
            ],
        ];
    }

    /**
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage There is not bundle registered.
     */
    public function testGenerateBundleCommandException()
    {
        $container = $this->getContainer();
        $container->setParameter('profideo.generator_bundle', []);

        $command = new GenerateBundleCommand();
        $command->setContainer($container);

        (new CommandTester($command))->execute([]);
    }
}
