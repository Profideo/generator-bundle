<?php

namespace Profideo\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\BundleGenerator as SensioBundleGenerator;

class BundleGenerator extends SensioBundleGenerator
{
    /** 
     * Overloads to generate only bundle class.
     * Moreover, parameters can be transmitted to the twig template.
     */
    public function generate($namespace, $bundle, $dir, $format, $structure, $parameters = [])
    {
        $dir .= '/'.strtr($namespace, '\\', '/');

        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != ['.', '..']) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $parameters += ['namespace' => $namespace, 'bundle' => $bundle, 'parent' => null];

        $this->renderFile('Bundle.php.twig', $dir.'/'.$bundle.'.php', $parameters);
    }
}
