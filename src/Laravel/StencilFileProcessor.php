<?php

namespace CodeStencil\Laravel;

use CodeStencil\Stencil;

class StencilFileProcessor
{
    public function __construct(protected string $path, protected array $variables)
    {
    }

    public function __invoke()
    {
        $contents = file_get_contents($this->path);

        if (!str_starts_with($contents, '<?php')) {
            return;
        }

        if (!str_contains($contents, 'return Stencil::make()') && !str_contains($contents, 'return \CodeStencil\Stencil::make()')) {
            return;
        }

        /** @var Stencil $stencil */
        $stencil = require $this->path;

        $stencil->variable($this->variables);

        $savePath = $this->resolveSavePath($stencil);

        $stencil->save($savePath);

        if ($savePath != $this->path) {
            unlink($this->path);
        }
    }

    private function resolveSavePath(Stencil $stencil): string
    {
        $reflectionClass = new \ReflectionClass($stencil);
        $prop            = $reflectionClass->getProperty('variables');
        $prop->setAccessible(true);

        $definedVariables = $prop->getValue($stencil);

        if (array_key_exists('overrideStubLocation', $definedVariables)) {
            return $definedVariables['overrideStubLocation'];
        }

        return $this->path;
    }
}
