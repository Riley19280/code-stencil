<?php

namespace CodeStencil;

use function CodeStencil\Utility\array_flatten;

trait CodeStencilHelpers
{
    public function comment(string $comment): static
    {
        return $this->addIndentedContent("// $comment\n");
    }

    public function multilineComment(string|array $comment): static
    {
        if (is_string($comment)) {
            $comment = explode("\n", $comment);
        }

        $lines = [
            '/*',
            ...array_map(fn($c) => " * $c", $comment),
            '*/',
        ];

        return $this->foreach($lines, fn(self $s, string $line) => $s->line($line));
    }

    public function phpdoc(string|array $summary = null, string|array $description = null, array $tags = null): static
    {
        if ($summary === null) {
            $summary = [];
        } elseif (is_string($summary)) {
            $summary = explode("\n", $summary);
        }

        if ($description === null) {
            $description = [];
        } elseif (is_string($description)) {
            $description = explode("\n", $description);
        }

        if ($tags === null) {
            $tags = [];
        }

        $hasSummary     = count($summary) > 0;
        $hasDescription = count($description) > 0;
        $hasTags        = count($tags) > 0;

        $lines = array_filter([
            $hasSummary || $hasDescription || $hasTags ? '/**' : null,
            ...array_map(fn($s) => " * $s", $summary),
            $hasSummary && ($hasDescription || $hasTags) ? ' *' : null,
            ...array_map(fn($d) => " * $d", $description),
            $hasDescription > 0 && $hasTags ? ' *' : null,
            ...array_flatten(array_map(function($key) use ($tags) {
                $tagLines = [];

                if (is_array($tags[$key])) {
                    $tagValues = $tags[$key];
                } else {
                    $tagValues = [$tags[$key]];
                }

                foreach ($tagValues as $tagVal) {
                    $tagLines[] = " * @$key $tagVal";
                }

                return $tagLines;
            }, array_keys($tags))),
            $hasSummary || $hasDescription || $hasTags ? ' */' : null,
        ]);

        return $this->foreach($lines, fn(self $s, string $line) => $s->line($line));
    }

    public function array(array $array): static
    {
        $isRecursiveCall = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)[1]['class'] === static::class;

        $this->line('[');

        foreach ($array as $key => $value) {
            $keyPart = '';
            if (!is_int($key)) {
                $keyPart = "'$key' => ";
            }

            $valuePart = match (true) {
                is_numeric($value) => "$value,",
                is_array($value)   => null,
                default            => "'$value',"
            };

            $this->line("{$keyPart}{$valuePart}");

            if (is_array($value)) {
                $this->withoutNewline()
                    ->indent()
                    ->array($value)
                    ->append(',')
                    ->deindent();
            }
        }

        if ($isRecursiveCall) {
            $this->deindent()->line(']')->indent();
        } else {
            $this->line(']');
        }

        return $this;
    }

    public function semicolon(): static
    {
        return $this->withoutNewline()->line(';');
    }

    public function wrapUsing(string $open, string $close, callable $callable, ...$args): static
    {
        return $this
            ->line($open)
            ->call($callable, ...$args)
            ->line($close);
    }

    /**
     * @param string                                           $statement
     * @param callable(static $stencil, mixed ...$args): mixed $callable
     * @param                                                  ...$args
     *
     * @return static
     */
    public function parenStatement(string $statement, callable $callable, ...$args): static
    {
        return $this
            ->line($statement)
            ->withoutNewline()
            ->wrapUsing('(', ')', $callable, ...$args);
    }

    /**
     * @param string                                           $statement
     * @param callable(static $stencil, mixed ...$args): mixed $callable
     * @param                                                  ...$args
     *
     * @return static
     */
    public function squareStatement(string $statement, callable $callable, ...$args): static
    {
        return $this
            ->line($statement)
            ->withoutNewline()
            ->wrapUsing('[', ']', $callable, ...$args);
    }

    /**
     * @param string                                           $statement
     * @param callable(static $stencil, mixed ...$args): mixed $callable
     * @param                                                  ...$args
     *
     * @return static
     */
    public function curlyStatement(string $statement, callable $callable, ...$args): static
    {
        return $this
            ->line($statement)
            ->withoutNewline()
            ->wrapUsing('{', '}', $callable, ...$args);
    }

    /**
     * @param string                                           $statement
     * @param callable(static $stencil, mixed ...$args): mixed $callable
     * @param                                                  ...$args
     *
     * @return static
     */
    public function angleStatement(string $statement, callable $callable, ...$args): static
    {
        return $this
            ->line($statement)
            ->withoutNewline()
            ->wrapUsing('<', '>', $callable, ...$args);
    }

    /**
     * @param string                                           $statement
     * @param callable(static $stencil, mixed ...$args): mixed $callable
     * @param                                                  ...$args
     *
     * @return static
     */
    public function angleClosedStatement(string $statement, callable $callable, ...$args): static
    {
        return $this
            ->line($statement)
            ->withoutNewline()
            ->wrapUsing('<', '/>', $callable, ...$args);
    }
}
