<?php

namespace Profideo\GeneratorBundle\Tests\Generator;

use Profideo\GeneratorBundle\Generator\BundleGenerator;
use Symfony\Component\Filesystem\Filesystem;

class BundleGeneratorTest extends \PHPUnit_Framework_TestCase
{
    private $tmpDir;

    public function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/bundle-generator-tests';
        $this->filesystem = new Filesystem();
        $this->filesystem->remove($this->tmpDir);
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->tmpDir);
    }

    public function testGenerateWithoutParent()
    {
        $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null);
        $this->assertTrue(file_exists($this->tmpDir.'/Foo/BarBundle/FooBarBundle.php'), 'FooBarBundle.php has been generated');

        $content = file_get_contents($this->tmpDir.'/Foo/BarBundle/FooBarBundle.php');
        $this->assertContains('namespace Foo\\BarBundle', $content);
        $this->assertNotContains('public function getParent()', $content);
    }

    public function testGenerateWithParent()
    {
        $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null, ['parent' => 'Baz']);
        $this->assertTrue(file_exists($this->tmpDir.'/Foo/BarBundle/FooBarBundle.php'), 'FooBarBundle.php has been generated');

        $content = file_get_contents($this->tmpDir.'/Foo/BarBundle/FooBarBundle.php');
        $this->assertContains('public function getParent()', $content);
    }

    public function testDirIsFile()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo');
        $this->filesystem->touch($this->tmpDir.'/Foo/BarBundle');

        try {
            $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->assertEquals('Unable to generate the bundle as the target directory "'.realpath($this->tmpDir.'/Foo/BarBundle').'" exists but is a file.', $e->getMessage());
        }
    }

    public function testIsNotWritableDir()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo/BarBundle');
        $this->filesystem->chmod($this->tmpDir.'/Foo/BarBundle', 0444);

        try {
            $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->filesystem->chmod($this->tmpDir.'/Foo/BarBundle', 0777);
            $this->assertEquals('Unable to generate the bundle as the target directory "'.realpath($this->tmpDir.'/Foo/BarBundle').'" is not writable.', $e->getMessage());
        }
    }

    public function testIsNotEmptyDir()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo/BarBundle');
        $this->filesystem->touch($this->tmpDir.'/Foo/BarBundle/somefile');

        try {
            $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null);
            $this->fail('An exception was expected!');
        } catch (\RuntimeException $e) {
            $this->filesystem->chmod($this->tmpDir.'/Foo/BarBundle', 0777);
            $this->assertEquals('Unable to generate the bundle as the target directory "'.realpath($this->tmpDir.'/Foo/BarBundle').'" is not empty.', $e->getMessage());
        }
    }

    public function testExistingEmptyDirIsFine()
    {
        $this->filesystem->mkdir($this->tmpDir.'/Foo/BarBundle');

        $this->getGenerator()->generate('Foo\BarBundle', 'FooBarBundle', $this->tmpDir, null, null);
    }

    protected function getGenerator()
    {
        $generator = new BundleGenerator($this->filesystem);
        $generator->setSkeletonDirs(__DIR__.'/../../Resources/skeleton');

        return $generator;
    }
}
