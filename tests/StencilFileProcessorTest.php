<?php

use CodeStencil\Laravel\RegistersOverrideStubLocationMacro;
use CodeStencil\Laravel\StencilFileProcessor;

uses(RegistersOverrideStubLocationMacro::class);

test('process file', function() {
    $realStubPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'LaravelStub.php';
    $tmpPath      = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'LaravelStub.php';
    copy($realStubPath, $tmpPath);

    (new StencilFileProcessor($tmpPath, ['i_name' => 'test']))();

    $contents = file_get_contents($tmpPath);

    expect($contents)->toMatchSnapshot();
});

test('process file custom location', function() {
    $realStubPath = __DIR__ . DIRECTORY_SEPARATOR . 'Fixtures' . DIRECTORY_SEPARATOR . 'LaravelStubCustomLocation.php';
    $tmpDir       = sys_get_temp_dir();
    $tmpPath      = $tmpDir . DIRECTORY_SEPARATOR . 'LaravelStubCustomLocation.php';
    copy($realStubPath, $tmpPath);

    $this->registerOverrideStubLocationMacro();

    (new StencilFileProcessor($tmpPath, ['i_name' => 'test']))();

    $contents = file_get_contents($tmpDir . DIRECTORY_SEPARATOR . 'Output.php');

    expect($contents)->toMatchSnapshot();

    expect(file_exists($tmpPath))->toBeFalse();
});
