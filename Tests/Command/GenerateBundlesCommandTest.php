<?php

namespace Profideo\GeneratorBundle\Tests\Command;

use Symfony\Component\Console\Tester\CommandTester;
use Sensio\Bundle\GeneratorBundle\Tests\Command\GenerateCommandTest;
use Profideo\GeneratorBundle\Command\GenerateBundlesCommand;

class GenerateBundlesCommandTest extends GenerateCommandTest
{
    /**
     * @dataProvider getTestGenerateBundlesCommandData
     */
    public function testGenerateBundlesCommand($configs, $expected)
    {
        $container = $this->getContainer();
        $container->setParameter('profideo_generator.bundles', $configs);

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
            ->getMockBuilder('Profideo\GeneratorBundle\Command\GenerateBundlesCommand')
            ->setMethods(['updateKernel'])
            ->getMock()
        ;

        $command->setGenerator($generator);
        $command->setContainer($container);

        (new CommandTester($command))->execute([]);
    }

    public function getTestGenerateBundlesCommandData()
    {
        return [
            [
                [
                    [
                        'name' => 'bundleTest',
                        'base_namespace' => 'TestNameSpace',
                        'parent' => null,
                        'class_prefix' => 'PrefixTest',
                    ],
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
                    [
                        'name' => 'bundleTest',
                        'base_namespace' => 'TestNameSpace',
                        'parent' => 'ParentBundleTest',
                        'class_prefix' => null,
                    ],
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
                    [
                        'name' => 'bundleTest',
                        'base_namespace' => 'TestNameSpace',
                        'parent' => 'ParentBundleTest',
                        'class_prefix' => 'PrefixTest',
                    ],
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
    public function testGenerateBundlesCommandException()
    {
        $container = $this->getContainer();
        $container->setParameter('profideo_generator.bundles', []);

        $command = new GenerateBundlesCommand();
        $command->setContainer($container);

        (new CommandTester($command))->execute([]);
    }
}
