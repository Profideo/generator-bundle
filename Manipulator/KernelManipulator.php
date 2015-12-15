<?php

namespace Profideo\GeneratorBundle\Manipulator;

use Symfony\Component\HttpKernel\KernelInterface;
use Sensio\Bundle\GeneratorBundle\Manipulator\Manipulator;

/**
 * @codeCoverageIgnore
 */
class KernelManipulator extends Manipulator
{
    protected $kernel;
    protected $reflected;

    /**
     * Constructor.
     *
     * @param KernelInterface $kernel A KernelInterface instance
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->reflected = new \ReflectionObject($kernel);
    }

    /**
     * Add bundle in method getGeneratedBundle of kernel. If method getGeneratedBundle does not exist, it is created.
     */
    public function addBundle($name, $class)
    {
        if (!$this->reflected->getFilename()) {
            return false;
        }

        try {
            $method = $this->reflected->getMethod('getGeneratedBundle');
        } catch (\ReflectionException $e) {
            // getGeneratedBundle method does not exist, creates it and adds new bundle in it
            file_put_contents(
                $this->reflected->getFilename(),
                str_replace(
                    '    public function registerBundles()',
                    '    public function getGeneratedBundle($name)'.PHP_EOL.
                    '    {'.PHP_EOL.
                    '        $bundles = array('.PHP_EOL.
                    "            '$name' => new $class(),".PHP_EOL.
                    '        );'.PHP_EOL.
                    PHP_EOL.
                    '        return isset($bundles[$name]) ? $bundles[$name] : null;'.PHP_EOL.
                    '    }'.PHP_EOL.
                    PHP_EOL.
                    '    public function registerBundles()',
                    file_get_contents($this->reflected->getFilename())
                )
            );

            return true;
        }

        // Adds new bundle in getGeneratedBundle method

        $src = file($this->reflected->getFilename());
        $lines = array_slice($src, $method->getStartLine() - 1, $method->getEndLine() - $method->getStartLine() + 1);

        // Don't add same bundle twice
        if (false !== strpos(implode('', $lines), $class)) {
            throw new \RuntimeException(sprintf('Bundle "%s" is already defined in "AppKernel::getGeneratedBundle()".', $class));
        }

        $this->setCode(token_get_all('<?php '.implode('', $lines)), $method->getStartLine());
        while ($token = $this->next()) {
            // $bundles
            if (T_VARIABLE !== $token[0] || '$bundles' !== $token[1]) {
                continue;
            }

            // =
            $this->next();

            // array start with traditional or short syntax
            $token = $this->next();
            if (T_ARRAY !== $token[0] && '[' !== $this->value($token)) {
                return false;
            }

            // add the bundle at the end of the array
            while ($token = $this->next()) {
                // look for ); or ];
                if (')' !== $this->value($token) && ']' !== $this->value($token)) {
                    continue;
                }

                if (';' !== $this->value($this->peek())) {
                    continue;
                }

                // ;
                $this->next();

                $lines = array_merge(
                    array_slice($src, 0, $this->line - 2),
                    // Appends a separator comma to the current last position of the array
                    array(rtrim(rtrim($src[$this->line - 2]), ',').','.PHP_EOL),
                    array(sprintf("            '$name' => new %s(),".PHP_EOL, $class)),
                    array_slice($src, $this->line - 1)
                );

                file_put_contents($this->reflected->getFilename(), implode('', $lines));

                return true;
            }
        }
    }
}
