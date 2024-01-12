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

        $stencil->save($this->path);
    }
}
