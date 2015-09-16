<?php

namespace Profideo\GeneratorBundle\Manipulator;

use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator as SensioKernelManipulator;

class KernelManipulator extends SensioKernelManipulator
{
    /**
     * Remove all bundles classes located in a namespace.
     *
     * @param string $namespace
     *
     * @return bool
     */
    public function removeNamespace($namespace)
    {
        $lines = file($this->reflected->getFilename());

        foreach ($lines as $lineIndex => &$line) {
            if (false !== strpos($line, $namespace)) {
                unset($lines[$lineIndex]);
            }
        }

        file_put_contents($this->reflected->getFilename(), implode('', $lines));
    }
}
