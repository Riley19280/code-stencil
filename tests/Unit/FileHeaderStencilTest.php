<?php

/** @noinspection MultipleExpectChainableInspection */

use CodeStencil\Stencil;

test('shebang', function() {
    expect(
        Stencil::make()
            ->shebang('bang')
            ->__toString()
    )->toBe("bang\n\n");
});

test('php shebang', function() {
    expect(
        Stencil::make()
            ->php()
            ->__toString()
    )->toBe("<?php\n\n");
});

test('strict types', function() {
    expect(
        Stencil::make()
            ->strictTypes()
            ->__toString()
    )->toBe("declare(strict_types = 1);\n\n");
});

test('namespace', function() {
    expect(
        Stencil::make()
            ->namespace('App\\Http')
            ->__toString()
    )->toBe("namespace App\Http;\n\n");
});

test('namespace with ;', function() {
    expect(
        Stencil::make()
            ->namespace('App\\Http;')
            ->__toString()
    )->toBe("namespace App\Http;\n\n");
});

test('use', function() {
    expect(
        Stencil::make()
            ->use('A')
            ->use('B', 'C')
            ->use(['D', 'E'])
            ->use('F', ['G', 'H'])
            ->__toString()
    )->toBe("use A;\nuse B;\nuse C;\nuse D;\nuse E;\nuse F;\nuse G;\nuse H;\n\n");

    expect(
        Stencil::make()
            ->use('A')
            ->use('A')
            ->use('A')
            ->__toString()
    )->toBe("use A;\n\n", 'expected unique uses');
});

test('all headers format', function() {
    expect(
        Stencil::make()
            ->php()
            ->strictTypes()
            ->namespace('App\\Http;')
            ->use('A', 'B')
            ->line('code starts here')
            ->__toString()
    )->toBe("<?php\n\ndeclare(strict_types = 1);\n\nnamespace App\Http;\n\nuse A;\nuse B;\n\ncode starts here\n");
});
