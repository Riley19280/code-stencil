---
title: Advanced Control Flow
sidebar_position: 8
---

### test

<FeatureHeader anchor="When" title>
When
</FeatureHeader>
The <strong><code>when</code></strong> method easily allow for conditional branching within a Stencil definition

```php
$myCondition = false;

Stencil::make()
->when($myCondition, fn(Stencil $stencil) => $stencil->line('My Condition passed'));
```

The first argument is your condition, and the second is what you would like to do in that condition


<FeatureHeader anchor="foreach" title>
Foreach
</FeatureHeader>

The <strong><code>foreach</code></strong> method allows for running code for each item in an array

The following will generate a variable and getter function for every item passed to the `foreach` function.

```php
Stencil::make()
->foreach(['item1', 'item2', 'item3'], function(Stencil $s, string $item) {
    $s->line("$$item = null;")
    ->curlyStatement("public function $item", fn(Stencil $s) => $s->line("return $this->$item"))
})
```

<FeatureHeader anchor="call" title>
Call
</FeatureHeader>
The <strong><code>call</code></strong> method allows you to call any other method or closure, passing in the current code stencil.
This allows you to make stencils composable, and easily break up complex logic.

```php
Stencil::make()
->curlyStatement('class MyClass', fn(Stencil $stencil) => $stencil
    ->call(function(Stencil $s) {
        // Do some complex logic and add stuff to the stencil
        $s->line('const ID = "my-class-id";');
    })
    ->call(...MyCodeGenerator::createClassVars()) // Using first class callables in php 8.1+
    ->call(...MyCodeGenerator::createClassMethods())
    ->call(...MyCodeGenerator::createToStringMethod())
    
)
```

<FeatureHeader anchor="merge">
The <strong><code>merge</code></strong> allows for the combining of many Stencils
</FeatureHeader>

When merging, all the defined variables, functions, and uses will also be merged.

```php
$a = Stencil::make()
->line('From Stencil A')
->use(StencilADependency::class)

$b = Stencil::make()
->line('From Stencil B')
->use(StencilBDependency::class)

$a->merge($b)->save('combined.txt')
// --- results in --- //
"use StencilADependency;
use StencilBDependency;

From Stencil A
From Stencil B
"
```

<FeatureHeader anchor="Macros">
The <strong><code>Macros</code></strong> method is an alias for a php shebang line
</FeatureHeader>
