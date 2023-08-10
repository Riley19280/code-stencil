---
title: Advanced Control Flow
sidebar_position: 8
---

### When
The **`when`** method easily allow for conditional branching within a Stencil definition
```php
$myCondition = false;

Stencil::make()
->when($myCondition, fn(Stencil $stencil) => $stencil->line('My Condition passed'));
```

The first argument is your condition, and the second is what you would like to do in that condition


### Foreach
The **`foreach`** method allows for running code for each item in an array
The following will generate a variable and getter function for every item passed to the `foreach` function.

```php
Stencil::make()
->foreach(['item1', 'item2', 'item3'], function(Stencil $s, string $item) {
    $s->line("$$item = null;")
    ->curlyStatement("public function $item", fn(Stencil $s) => $s->line("return $this->$item"))
})
```

### Call
The **`call`** method allows you to call any other method or closure, passing in the current code stencil.

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

### Merge
The **`merge`** allows for the combining of many Stencils

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

### Macro
The **`Macro`** method allows you to add custom functions to the `CodeStencil` class. 
You can read more about macros [here](https://medium.com/@sirajul.anik/laravel-macroable-understanding-macros-and-mixin-1c3aaa9f8ba8)
