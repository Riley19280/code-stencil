<?php

namespace CodeStencil;

use Closure;
use function CodeStencil\Utility\array_flatten;
use Illuminate\Support\Traits\Conditionable;
use Illuminate\Support\Traits\Macroable;

class Stencil
{
    use CodeStencilHelpers;
    use Conditionable;
    use FileHeaderStencil;
    use LaravelStringHelpers;
    use Macroable;

    protected string $content = '';

    protected int $indentLevel          = 0;
    protected int $spacesPerIndentLevel = 4;

    protected bool $isDryRun = false;

    protected array $variables = [];
    protected array $functions = [];

    protected bool $formattingEnabled = true;
    protected ?Closure $formatter     = null;

    public function __construct()
    {
        $this->registerLaravelStringFunctions();
    }

    public static function make(): Stencil
    {
        return new Stencil();
    }

    protected function addContent(string $content): static
    {
        $this->content .= $content;

        return $this;
    }

    protected function addIndentedContent(string $content): static
    {
        // Handle cases where we are just adding newlines, and we don't want indent
        if (empty(trim($content))) {
            return $this->addContent($content);
        } else {
            return $this->addContent(str_repeat(' ', $this->indentLevel * $this->spacesPerIndentLevel) . $content);
        }
    }

    public function newline(int $number = 1): static
    {
        return $this->addContent(str_repeat("\n", $number));
    }

    public function line(string $line = ''): static
    {
        return $this->addIndentedContent($line . "\n");
    }

    public function append(string $text): static
    {
        return $this->withoutNewline()->addContent($text)->newline();
    }

    public function spacesPerIndent(int $spaces = 4): static
    {
        $this->spacesPerIndentLevel = max($spaces, 0);

        return $this;
    }

    /**
     * @param string|callable(static $stencil, mixed ...$args): mixed $code
     * @param int                                                     $indentLevel
     *
     * @return static
     */
    public function indented(string|callable $code, int $indentLevel = 1): static
    {
        // @phpstan-ignore-next-line
        return $this
            ->indent($indentLevel)
            ->when(is_string($code), fn(self $s) => $s->line($code), fn(self $s) => $s->call($code))
            ->deindent($indentLevel);
    }

    public function setIndentLevel(int $indent = 0): static
    {
        $this->indentLevel = max($indent, 0);

        return $this;
    }

    public function indent(int $indent = 1): static
    {
        $this->indentLevel += $indent;

        return $this;
    }

    public function deindent(int $indent = 1): static
    {
        $this->indentLevel = max($this->indentLevel - $indent, 0);

        return $this;
    }

    public function withoutNewline(): static
    {
        $this->content = preg_replace('/\\n$/', '', $this->content);

        return $this;
    }

    public function save(string $path): void
    {
        if ($this->isDryRun) {
            return;
        }

        $path = $this->substituteVariables($path);
        $path = $this->applyFunctions($path);

        if (!file_exists(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        file_put_contents($path, $this->__toString());

        $this->format($path);
    }

    public function disableFormat(bool $enabled = false): static
    {
        $this->formattingEnabled = $enabled;

        return $this;
    }

    public function setFormatter(callable $formatter): static
    {
        $this->formatter = $formatter(...);

        return $this;
    }

    public function dryRun(bool $dryRun = true): static
    {
        $this->isDryRun = $dryRun;

        return $this;
    }

    /**
     * @param iterable                                         $items
     * @param callable(static $stencil, mixed ...$args): mixed $handler
     * @param                                                  ...$args
     *
     * @return $this
     */
    public function foreach(iterable $items, callable $handler, ...$args): static
    {
        foreach ($items as $item) {
            $handler($this, $item, ...$args);
        }

        return $this;
    }

    /**
     * @param callable(static $stencil, mixed ...$args): mixed $handler
     * @param                                                  ...$args
     *
     * @return $this
     */
    public function call(callable $handler, ...$args): static
    {
        $result = $handler($this, ...$args);

        return $result ?? $this;
    }

    public function variable(string|array $name, string $value = ''): static
    {
        if (is_array($name)) {
            foreach ($name as $key => $value) {
                $this->variables[$key] = $value;
            }
        } else {
            $this->variables[$name] = $value;
        }

        return $this;
    }

    /**
     * @param string                                           $name
     * @param callable(static $stencil, mixed ...$args): mixed $function
     *
     * @return $this
     */
    public function function(string $name, callable $function): static
    {
        $this->functions[$name] = $function;

        return $this;
    }

    protected function substituteVariables(string $content): string
    {
        $patterns     = [];
        $replacements = [];

        $delimiter = '/';

        foreach ($this->variables as $variable => $value) {
            if (str_starts_with($variable, '/') && str_ends_with($variable, '/')) {
                $patterns[]     = $variable;
                $replacements[] = $value;

            } else {
                $patterns[]     = "{$delimiter}{$variable}{$delimiter}";
                $replacements[] = $value;
            }
        }

        $res = preg_replace($patterns, $replacements, $content);

        if ($res === null) {
            throw new \Error('preg_replace error: ' . preg_last_error_msg(), preg_last_error());
        }

        return $res;
    }

    protected function applyFunctions(string $content): string
    {
        preg_match_all('/%.*?% *\(.*?\)/', $content, $functionMatches);

        foreach ($functionMatches[0] as $functionCallSignature) {
            preg_match('/(?<=%).*?(?=%)/', $functionCallSignature, $functionNames);
            preg_match('/(?<=\().*?(?=\))/', $functionCallSignature, $args);

            $functionNames = array_filter(array_flatten(array_map(fn($f) => preg_split('/[,| ]/', $f), $functionNames)));
            $args          = array_filter(array_flatten(array_map(fn($a) => preg_split('/[,| ]/', $a), $args)));

            $allValidFunctions = array_reduce($functionNames, fn($acc, $f) => $acc && array_key_exists($f, $this->functions), true);

            if (!count($functionNames) || !$allValidFunctions) {
                continue;
            }

            $result = [$functionCallSignature];

            foreach (array_reverse($functionNames) as $functionName) {
                $result = $this->functions[$functionName](...$args);

                if (!is_array($result)) {
                    $result = [$result];
                }
                $args = $result;
            }

            $content = str_replace($functionCallSignature, $result[0], $content);
        }

        return $content;
    }

    public function merge(Stencil $stencil): static
    {
        $this->content = $this->content . $stencil->content;

        $this->indentLevel          = $stencil->indentLevel;
        $this->spacesPerIndentLevel = $stencil->spacesPerIndentLevel;

        $this->isDryRun = $stencil->isDryRun ?? $this->isDryRun;

        $this->variables = [...$this->variables, ...$stencil->variables];
        $this->functions = [...$this->functions, ...$stencil->functions];

        $this->shebang     = $stencil->shebang ?? $this->shebang;
        $this->namespace   = $stencil->namespace ?? $this->namespace;
        $this->strictTypes = $stencil->strictTypes ?? $this->strictTypes;
        $this->classUses   = [...$this->classUses, ...$stencil->classUses];

        return $this;
    }

    protected function format(string $path): void
    {
        if ($this->isDryRun) {
            return;
        }

        $this->getStencilFormatter()($path);
    }

    protected function getStencilFormatter(): callable
    {
        if ($this->formatter) {
            return $this->formatter;
        } else {
            return new StencilFormatter();

        }
    }

    public function __toString(): string
    {
        $content = implode('', array_filter([
            $this->fileHeaderInfo(),
            $this->content,
        ]));

        $content = $this->substituteVariables($content);
        $content = $this->applyFunctions($content);

        return $content;
    }
}
