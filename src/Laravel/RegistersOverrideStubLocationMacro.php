<?php

namespace CodeStencil\Laravel;

use CodeStencil\Stencil;

trait RegistersOverrideStubLocationMacro
{
    private function registerOverrideStubLocationMacro(): void
    {
        Stencil::macro('overrideStubLocation', function(string $path) {
            $newPath = $this->substituteVariables($path);
            $newPath = $this->applyFunctions($newPath);

            $this->variable('overrideStubLocation', $newPath);

            return $this;
        });
    }
}
