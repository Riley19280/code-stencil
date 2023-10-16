
<p align="center">
    <img src="https://raw.githubusercontent.com/Riley19280/code-stencil/main/docs/static/img/splash.png" width="600" alt="Code Stencil">
    <p align="center">
        <a href="https://github.com/riley19280/code-stencil/actions"><img alt="GitHub Workflow Status (master)" src="https://img.shields.io/github/actions/workflow/status/riley19280/code-stencil/run-tests.yml?branch=main&label=Tests"></a>
        <a href="https://packagist.org/packages/code-stencil/code-stencil"><img alt="Total Downloads" src="https://img.shields.io/packagist/dt/code-stencil/code-stencil"></a>
        <a href="https://packagist.org/packages/code-stencil/code-stencil"><img alt="Latest Version" src="https://img.shields.io/packagist/v/code-stencil/code-stencil"></a>
        <a href="https://packagist.org/packages/code-stencil/code-stencil"><img alt="License" src="https://img.shields.io/packagist/l/code-stencil/code-stencil"></a>
    </p>
</p>

# Introduction

Code Stencil provides an elegant, easy to read pattern that is great for generating stub files,
and will streamline your code generation tasks. A common occurrence with code generation is that readability and maintenance
suffer, but with Code Stencil, it's a piece of cake!

## Create a class
```php
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
    ->save('MyClass.php')
```

<details>
<summary>Show Result</summary>

```php
<?php

declare(strict_types = 1);

namespace App\Models;

use OtherClass;

class MyClass
{
    private OtherClass $otherClass;

    /**
     * Create a new instance of MyClass
     * @param OtherClass $class
     * @param bool       $options
     */
    function __construct(OtherClass $class, bool $options = false)
    {
        $this->otherClass = $class;
    }
}
```
</details>

It can also become much more dynamic, with the addition of [variables](/docs/walkthrough/functions-and-variables#Variables), [functions](/docs/walkthrough/functions-and-variables#Functions), and [conditional logic](/docs/walkthrough/advanced-control-flow#When-&-Unless).

## Variables, functions, and conditionals
```php
$properties = ['Id', 'Name', 'StartAt'];

$includeTimestamp = false;

Stencil::make()
    ->php()
    ->variable('__class__', 'MyClass')
    ->function('lcfirst', fn(string $value) => lcfirst($value))
    ->curlyStatement('class __class__', fn(Stencil $s) => $s
        ->foreach($properties, fn(Stencil $s, string $property) => $s
            ->line("private string % lcfirst %($property);") // We can call our 'lcfirst' function that we defined above using this syntax
        )
        ->when($includeTimestamp, fn(Stencil $s) => $s->line('private DateTime $timestamp;'))
    )
    ->save('__class__.php') // File output will be to MyClass.php
```

<details>
<summary>Show Result</summary>

```php
<?php

class MyClass {
    private string id;
    private string name;
    private string startAt;
}
```
</details>

This example is basic, but there's no limit to the complexity of code you can generate!
