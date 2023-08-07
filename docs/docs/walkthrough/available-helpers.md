---
title: Available Helpers
sidebar_position: 7
---

Out of the box Code Stencil provides many utility functions to make code generation a breeze!

<FeatureHeader anchor="comment">
The <strong><code>comment</code></strong> method adds a simple comment
</FeatureHeader>

```php
Stencil::make()
->comment('Hello')
// --- results in --- // 
"// Hello
"
```

<FeatureHeader anchor="multilineComment">
The <strong><code>multilineComment</code></strong> method adds a comment with line breaks included
</FeatureHeader>

```php
Stencil::make()
->multilineComment("Hello\nWorld")
// --- results in --- // 
"
/*
* Hello
* World
*/
"
```

<FeatureHeader anchor="phpdoc">
The <strong><code>phpdoc</code></strong> method can generate a php docblock for you
</FeatureHeader>

```php
Stencil::make()
->phpdoc(
    summary: 'Compare the two values',
    description: 'Compare 2 strings using some fancy logic',
    tags: [
        'see' => 'OtherCompare::class',
        'param' => ['string $value1', 'string $value2'],
    ]
)
// --- results in --- // 
'/**
 * Compare the two values
 *
 * Compare 2 strings using some fancy logic
 *
 * @see OtherCompare::class
 * @param string $value1
 * @param string $value2
 */
'
```

<FeatureHeader anchor="array">
The <strong><code>array</code></strong> method will print the passed array into the Stencil
</FeatureHeader>

```php
Stencil::make()
->array([
    'key1' => 'value1',
    'key2' => [
        'a',
        'b',
        'c',
    ]
])
// --- results in --- // 
[
    'key1' => 'value1',
    'key2' => [
        'a',
        'b',
        'c',
    ]
]
```


<FeatureHeader anchor="semicolon">
The <strong><code>semicolon</code></strong> method adds a semicolon
</FeatureHeader>

```php
Stencil::make()
->line('$variable = "value"')->semicolon();
// --- results in --- // 
"$variable = "value"; 
"
```

Several methods are also provided that help manage opening and closing braces
For example, the `curlyStatement` method can help when generating a function definition

```php
Stencil::make()
->curlyStatement('function getValue()', fn(Stencil $s) => $s
    ->line('return $this->value;')
)
// --- results in --- //
function getValue() {
    return $this->value;
}
```

| Method               | Wrappers     |
|----------------------|--------------|
| parenStatement       | `(` and `)`  |
| squareStatement      | `[` and `]`  |
| curlyStatement       | `{` and `}`  |
| angleStatement       | `<` and `>`  |
| angleClosedStatement | `<` and `/>` |


# Laravel Specific Helpers

When installed in a Laravel application, all the available string utilities are registered as functions, and can be used within Stencils.
For example, `camel` and `snake`
```php
Stencil::make()
->line('% camel %(my-value))
->line('% snake %(my-value))
// --- results in --- //
"myValue
my_value
"
```

A list of the functions can be found [here](https://laravel.com/api/10.x/Illuminate/Support/Stringable.html)

If you would not like these helper methods to be added, then you can call the following function before creating your stencil class 
```php
Stencil::disableLaravelStringHelpers()
```
