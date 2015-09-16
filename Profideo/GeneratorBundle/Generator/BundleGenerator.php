<?php

namespace Profideo\GeneratorBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Generator\BundleGenerator as SensioBundleGenerator;

class BundleGenerator extends SensioBundleGenerator
{
    /** 
     * Overload to generate only bundle class.
     */
    public function generate($namespace, $bundle, $dir, $format, $structure, $parameters=[])
    {
        $dir .= '/'.strtr($namespace, '\\', '/');

        if (file_exists($dir)) {
            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the broadcat client bundle as the target directory "%s" exists but is a file.', realpath($dir)));
            }
            $files = scandir($dir);
            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf('Unable to generate the broadcat client bundle as the target directory "%s" is not empty.', realpath($dir)));
            }
            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf('Unable to generate the broadcat client bundle as the target directory "%s" is not writable.', realpath($dir)));
            }
        }

        $parameters = array_merge($parameters, ['namespace' => $namespace, 'bundle' => $bundle]);
 
        $this->renderFile('Bundle.php.twig', $dir.'/'.$bundle.'.php', $parameters);
    }
}
