<?php

namespace CodeStencil;

trait FileHeaderStencil
{
    protected ?string $shebang     = null;
    protected ?string $namespace   = null;
    protected ?string $strictTypes = null;
    protected array $classUses     = [];

    public function php(): static
    {
        return $this->shebang('<?php');
    }

    public function shebang(string $shebang): static
    {
        $this->shebang = $shebang;

        return $this;
    }

    public function strictTypes(): static
    {
        $this->strictTypes = 'declare(strict_types = 1);';

        return $this;
    }

    /**
     * @param class-string $namespace
     */
    public function namespace(string $namespace): static
    {
        $this->namespace = 'namespace ' . $namespace;
        if (!str_contains($this->namespace, ';')) {
            $this->namespace .= ';';
        }

        return $this;
    }

    /**
     * @param class-string[]|class-string ...$classes
     */
    public function use(array|string ...$classes): static
    {
        $args = func_get_args();
        array_walk_recursive($args, fn($use) => $this->classUses[] = $use);

        return $this;
    }

    protected function fileHeaderInfo(): string
    {
        return implode('', array_filter([
            $this->shebang,
            $this->shebang ? "\n\n" : null,
            $this->strictTypes,
            $this->strictTypes ? "\n\n" : null,
            $this->namespace,
            $this->namespace ? "\n\n" : null,
            ...array_map(fn($u) => "use $u;\n", array_unique($this->classUses)),
            count($this->classUses) ? "\n" : null,
        ]));
    }
}
