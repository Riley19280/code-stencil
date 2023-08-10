---
title: Functions & Variables
sidebar_position: 6
---

Often times when generating code, we need variants of something, such as a name in camel case, or in snake case.
Functions and Variables can be leveraged to make those situations easy.

### Variables

Variables are defined as such:

```php
Stencil::make()
->variable('_name_', 'testing')
```

and can be used by referencing the string in the template

```php
Stencil::make()
->variable('_name_', 'testing')
->line('_name_')
// --- results in --- //
"testing
"
```

In these examples, variables start and end with and underscore. 
This is not a requirement for variables, as variables act as simple find and replace, 
but helps prevent accidental replacements and makes them easily identifiable  

If you would like, variables also support regex when the string starts and ends with `/`

```php
Stencil::make()
->variable('/.*!/', 'excitement!')
->line('variables!')
->line('are!')
->line('cool!')
// --- results in --- //
"excitement!
excitement!
excitement!
"
```

:::tip
<details>
<summary>Variables can contain other variables</summary>

```php
Stencil::make()
->variable('test', 'intermediate')
->variable('intermediate', 'value')
->line('test')
->__toString()
// --- results in --- //
"value
"
```

Note that the order in which variables are defined matters for this, as variables will be evaluated in the order in which they are defined.
In the example above, swapping the order will cause the result to be `intermediate` instead of `value`
</details>
:::

### Functions

Function implementations are defined like so

```php
Stencil::make()
->function('functionName', function(string $input) {
    // return whatever you would like to be printed in the stencil
    return strtoupper($input)
})
```

And can be used within a stencil like this:

```php
Stencil::make()
->function('functionName', fn(string $input) => strtoupper($input))
->line('% functionName %(test)')
// --- results in --- //
"
TEST
"
```

A function definition within a stencil looks like this `% <name of function> %(<arguments>)`.
The `<arguments>` can be a comma separated list of values to pass to the funciton

### Function Chaining

Function calls can also be chained, by providing multiple function names between the `%` signs.
These functions are evaluated from right to left, and the result of the previous function will be passed as the argument to the next.

```php
Stencil::make()
->function('ucfirst', fn(string $v) => ucfirst($v))
->function('substr', fn(string $v, $i) => substr($v, $i))
->line('% ucfirst substr %(test, 1)')
// --- results in --- //
"Est
"
// First, substr is called as `substr('test', 1)`, returning est
// Then ucfirst is called as `ucfirst('est')`, returning Est
```

:::tip

Functions are evaluated __after__ variables 

This means that you can define a function call as a variable, and then reference it wherever you would like!

```php
\CodeStencil\Stencil::make()
->variable('_Name_', '% ucfirst %(_name_)')
->variable('_name_', 'myName')
->function('ucfirst', fn($val) => ucfirst($val))
->line('_Name_')
// --- results in --- //
"MyName
"
```

And of course your function can be as simple or complex as you would like!

:::
