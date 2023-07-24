<?php

/** @noinspection MultipleExpectChainableInspection */

use CodeStencil\Stencil;

test('comment', function() {
    expect(
        Stencil::make()
            ->comment('test')
            ->__toString()
    )
        ->toBe("// test\n");
});

test('multiline comment', function() {
    expect(
        Stencil::make()
            ->multilineComment('test')
            ->__toString()
    )->toBe("/*\n * test\n*/\n", 'expected a simple multiline comment');

    expect(
        Stencil::make()
            ->multilineComment(['test1', 'test2'])
            ->__toString()
    )->toBe("/*\n * test1\n * test2\n*/\n", 'expected to handle array items as separate lines');

    expect(
        Stencil::make()
            ->multilineComment("test1\ntest2")
            ->__toString()
    )->toBe("/*\n * test1\n * test2\n*/\n", 'expected to handle newlines in the provided string');

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->multilineComment("test1\ntest2")
            ->__toString()
    )->toBe(" /*\n  * test1\n  * test2\n */\n", 'expected indent to be handled');
});

test('phpdoc', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->phpdoc()
            ->__toString()
    )->toBe('', 'expected nothing to be generated');

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->phpdoc(summary: 'summary')
            ->__toString()
    )->toBe(" /**\n  * summary\n  */\n");

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->phpdoc(description: 'description')
            ->__toString()
    )->toBe(" /**\n  * description\n  */\n");

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->phpdoc(tags: ['tag' => 'value'])
            ->__toString()
    )->toBe(" /**\n  * @tag value\n  */\n");

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent()
            ->phpdoc('summary', 'description', ['tag' => 'value'])
            ->__toString()
    )->toBe(" /**\n  * summary\n  *\n  * description\n  *\n  * @tag value\n  */\n", 'expected all attributes to be handled');
});

test('array', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent(1)
            ->array([
                'noKey1',
                'noKey2',
                'key1'    => 'value1',
                'key2'    => 'value2',
                'nested1' => [
                    'nested2' => [
                        'nested3' => 'nestedvalue',
                    ],
                ],
            ])
            ->line('same indent')
            ->__toString()
    )->toBe(" [\n 'noKey1',\n 'noKey2',\n 'key1' => 'value1',\n 'key2' => 'value2',\n 'nested1' =>   [\n  'nested2' =>    [\n   'nested3' => 'nestedvalue',\n  ],\n ],\n ]\n same indent\n");
});

test('wrap statement', function() {
    expect(
        Stencil::make()
            ->wrapUsing('(', ')', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("(\ntest\n)\n");
});

test('parenthesis statement', function() {
    expect(
        Stencil::make()
            ->parenStatement('function', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("function(\ntest\n)\n");
});

test('square statement', function() {
    expect(
        Stencil::make()
            ->squareStatement('function', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("function[\ntest\n]\n");
});

test('curly statement', function() {
    expect(
        Stencil::make()
            ->curlyStatement('function', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("function{\ntest\n}\n");
});

test('angle statement', function() {
    expect(
        Stencil::make()
            ->angleStatement('function', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("function<\ntest\n>\n");
});

test('angle closed statement', function() {
    expect(
        Stencil::make()
            ->angleClosedStatement('function', fn(Stencil $s) => $s->line('test'))
            ->__toString()
    )->toBe("function<\ntest\n/>\n");
});

test('semicolon', function() {
    expect(
        Stencil::make()
            ->line('test')
            ->semicolon()
            ->__toString()
    )->toBe("test;\n");
});

test('laravel helpers registered', function() {
    $stencil = Mockery::mock(Stencil::class)->makePartial();
    $stencil->shouldAllowMockingProtectedMethods();
    $stencil->shouldReceive('isLaravelEnvironment')->andReturn(true)->once();
    $stencil->shouldReceive('getLaravelStringMethods')->andReturn(['laravelStrFn'])->once();

    $stencil->registerLaravelStringFunctions();

    expect(
        invade($stencil)
            ->functions
    )->toHaveKey('laravelStrFn');
});

test('disable laravel helpers', function() {
    $stencil = Mockery::mock(Stencil::class)->makePartial();
    $stencil->shouldAllowMockingProtectedMethods();
    $stencil->shouldReceive('isLaravelEnvironment')->andReturn(true)->never();
    $stencil->shouldReceive('getLaravelStringMethods')->andReturn(['laravelStrFn'])->never();

    $stencil->disableLaravelStringHelpers();
    $stencil->registerLaravelStringFunctions();

    expect(
        invade($stencil)
            ->functions
    )->toBeEmpty();
});
