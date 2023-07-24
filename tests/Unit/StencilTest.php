<?php

/** @noinspection MultipleExpectChainableInspection */

use CodeStencil\Stencil;

//describe('content utility', function() {

test('add raw', function() {
    expect(
        invade(Stencil::make())
            ->addContent('raw content')
            ->__toString()
    )->toBe('raw content');
});

test('add raw indented', function() {
    expect(
        invade(Stencil::make()->indent())
            ->addIndentedContent('raw content')
            ->__toString()
    )->toBe('    raw content');
});

test('line', function() {
    expect(
        Stencil::make()
            ->line('test')
            ->__toString()
    )
        ->toBe("test\n");
});

test('append', function() {
    expect(
        Stencil::make()
            ->line('hello')
            ->append(' world')
            ->__toString()
    )
        ->toBe("hello world\n");
});

test('add newlines', function() {
    expect(
        Stencil::make()
            ->newline(2)
            ->__toString()
    )->toBe("\n\n");
});

test('add newline via line', function() {
    expect(
        Stencil::make()
            ->line()
            ->__toString()
    )->toBe("\n");
});

test('add indented', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indented('test')
            ->__toString()
    )
        ->toBe(" test\n");

    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indented(fn($s) => $s->line('test'))
            ->__toString()
    )
        ->toBe(" test\n");
});

test('set indent level', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->setIndentLevel(1)
            ->line('test')
            ->setIndentLevel(2)
            ->line('test')
            ->__toString()
    )
        ->toBe(" test\n  test\n");

    expect(
        invade(Stencil::make()
            ->setIndentLevel(-1)
        )->indentLevel
    )
        ->toBe(0);
});

test('increase indent', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent(2)
            ->line('test')
            ->indent(2)
            ->line('test')
            ->__toString()
    )
        ->toBe("  test\n    test\n");
});

test('decrease indent', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(1)
            ->indent(4)
            ->line('test')
            ->deindent(2)
            ->line('test')
            ->__toString()
    )
        ->toBe("    test\n  test\n");
});

test('set spaces for indent', function() {
    expect(
        Stencil::make()
            ->spacesPerIndent(2)
            ->indent()
            ->line('test')
            ->__toString()
    )
        ->toBe("  test\n");


    expect(
        invade(Stencil::make()
            ->spacesPerIndent(-1)
        )->spacesPerIndentLevel
    )
        ->toBe(0);
});

test('save', function() {
    $path = sys_get_temp_dir() . '/save';
    Stencil::make()
        ->php()
        ->line('test')
        ->save($path);

    expect($path)->toBeReadableFile()
        ->and(file_get_contents($path))->toBe("<?php\n\ntest\n");
});

test('dry run save', function() {
    $path = sys_get_temp_dir() . '/save-dry-run';
    Stencil::make()
        ->php()
        ->dryRun()
        ->line('test')
        ->save($path);

    expect($path)->not->toBeReadableFile();
});

test('foreach', function() {
    expect(
        Stencil::make()
            ->foreach([1, 2, 3], fn(Stencil $s, $i) => $s->line($i))
            ->__toString()
    )
        ->toBe("1\n2\n3\n");
});

test('foreach with extra args', function() {
    $extraArg = 'test';
    expect(
        Stencil::make()
            ->foreach([1, 2, 3], fn(Stencil $s, $i, $ea) => $s->line("$ea $i"), $extraArg)
            ->__toString()
    )
        ->toBe("test 1\ntest 2\ntest 3\n");
});

test('call', function() {
    expect(
        Stencil::make()
            ->call(fn(Stencil $s) => $s->line('from call'))
            ->__toString()
    )
        ->toBe("from call\n");
});

test('call first class callable', function() {

    $cls = new class()
    {
        public function test(Stencil $s): Stencil
        {
            return $s->line('first class callable');
        }
    };

    $instance = new $cls;

    expect(
        Stencil::make()
            ->call($instance->test(...))
            ->__toString()
    )
        ->toBe("first class callable\n");
})->skip(fn() => version_compare(phpversion(), '8.1', '<'));

test('macro', function() {
    Stencil::macro('appendCustomContent', function() {
        $this->append('my custom content');

        return $this;
    });

    expect(Stencil::hasMacro('appendCustomContent'))->toBeTrue();

    expect(
        Stencil::make()
            ->appendCustomContent()
            ->__toString()
    )
        ->toBe("my custom content\n");
});

test('register variables', function() {
    expect(
        invade(Stencil::make()
            ->variable('test', 'value')
            ->variable([
                'test1' => 'value1',
                'test2' => 'value2',
            ]))
            ->variables
    )
        ->toBe([
            'test'  => 'value',
            'test1' => 'value1',
            'test2' => 'value2',
        ]);
});

test('use variables', function() {
    expect(
        Stencil::make()
            ->variable('test', 'value')
            ->line('test')
            ->__toString()
    )
        ->toBe("value\n");

    expect(
        Stencil::make()
            ->variable('test', 'intermediate')
            ->variable('intermediate', 'value')
            ->line('test')
            ->__toString()
    )
        ->toBe("value\n", 'variables should replace other variables');

    expect(
        Stencil::make()
            ->variable('/t.+!/', 'value')
            ->line('test!')
            ->line('testing!')
            ->line('tested!')
            ->__toString()
    )
        ->toBe("value\nvalue\nvalue\n", 'regex should be respected');
});

test('register function', function() {
    expect(
        invade(Stencil::make()
            ->function('test', fn() => ''))
            ->functions
    )
        ->toHaveKey('test');
});

test('use function', function() {
    expect(
        Stencil::make()
            ->function('ucfirst', fn(string $v) => ucfirst($v))
            ->line('% ucfirst %(test)')
            ->__toString()
    )
        ->toBe("Test\n");

    expect(
        Stencil::make()
            ->function('ucfirst', fn(string $v) => ucfirst($v))
            ->function('substr', fn(string $v, $i) => substr($v, $i))
            ->line('% ucfirst substr %(test, 1)')
            ->__toString()
    )
        ->toBe("Est\n", 'chained function calls with arguments');
});

test('merge', function() {
    $stencil = Stencil::make()
        ->line('main')
        ->setIndentLevel(1)
        ->spacesPerIndent(1)
        ->variable('mainVar', 'value')
        ->function('mainFunc', fn() => '')
        ->shebang('bang')
        ->php()
        ->namespace('namespace');

    $stencil2 = Stencil::make()
        ->line('merged')
        ->setIndentLevel(2)
        ->spacesPerIndent(2)
        ->dryRun()
        ->variable('mergeVar', 'value')
        ->function('mergeFunc', fn() => '')
        ->php()
        ->namespace('merge')
        ->strictTypes()
        ->use('myClass');

    $mergedStencil = invade($stencil->merge($stencil2));

    expect($mergedStencil->content)->toBe("main\nmerged\n");
    expect($mergedStencil->spacesPerIndentLevel)->toBe(2);
    expect($mergedStencil->isDryRun)->toBeTrue();
    expect($mergedStencil->variables)->toHaveKeys(['mainVar', 'mergeVar']);
    expect($mergedStencil->functions)->toHaveKeys(['mainFunc', 'mergeFunc']);
    expect($mergedStencil->shebang)->toBe('<?php');
    expect($mergedStencil->namespace)->toBe('namespace merge;');
    expect($mergedStencil->strictTypes)->toBe('declare(strict_types = 1);');
    expect($mergedStencil->classUses)->toBe(['myClass']);
});

//});
