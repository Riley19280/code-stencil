<?php

/** @noinspection PhpIllegalPsrClassPathInspection */

use CodeStencil\Stencil;

class OtherClass
{
}

test('create a class', function() {
    expect(
        Stencil::make()
            ->php()
            ->strictTypes()
            ->namespace('App/Models')
            ->curlyStatement('class MyClass', fn(Stencil $s) => $s
                ->line('private OtherClass $otherClass;')
                ->use(OtherClass::class) // This will add a use statement to the file
                ->line()
                ->phpdoc(
                    summary: 'Create a new instance of MyClass',
                    tags: ['param' => ['OtherClass $class', 'bool $options']]
                )
                ->curlyStatement('function __construct(OtherClass $class, bool $options = false)', fn(Stencil $s) => $s
                    ->line('$this->otherClass = $class;')
                )
            )
    )->toMatchSnapshot();
});

test('foreach and functions', function() {
    $properties = ['Id', 'Name', 'StartAt'];

    $includeTimestamps = false;

    expect(
        Stencil::make()
            ->php()
            ->variable('__class__', 'MyClass')
            ->function('lcfirst', fn(string $value) => lcfirst($value))
            ->curlyStatement('class __class__', fn(Stencil $s) => $s
                ->foreach($properties, fn(Stencil $s, string $property) => $s
                    ->line("private string % lcfirst %($property);")
                )
                ->when($includeTimestamps, fn(Stencil $s) => $s->line('private DateTime $timestamp;'))
            )
        //        ->save('__class__.php')
    )->toMatchSnapshot();
});

test('laravel controller stub', function() {
    expect(
        Stencil::make()
            ->php()
            ->variable('__namespace__', 'namespace')
            ->variable('__rootNamespace__', 'rootNamespace')
            ->namespace('__namespace__')
            ->use('__rootNamespace__\Http\Controllers\Controller')
            ->use('Illuminate\Http\Request')
            ->curlyStatement('class __class__ extends Controller', function() {
            })
    )->toMatchSnapshot();
});
