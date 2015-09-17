<?php

namespace Profideo\GeneratorBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ProfideoGeneratorBundle extends Bundle
{
    public function getParent()
    {
        return 'SensioGeneratorBundle';
    }
}
