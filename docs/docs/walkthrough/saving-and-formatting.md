---
title: Saving and Formatting
sidebar_position: 5
---

### Save
The **`save`** method will save your code stencil to a file

If the provided path does not exist, then it will be created.

```php
Stencil::make()
->line('Hello World!')
->save('/my/path/file.txt')
```

### DryRun
The **`dryRun`** method, indicates that the file should not be saved to the disk.

```php
Stencil::make()
->line('Hello World!')
->line('World!')
->dryRun()
->save('/my/path/file.txt')
// Output file will not be created 
```

---

:::info

Code Stencils provide a variety of ways to format your code after its generated. 
By default, if you have [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer), [Pint](https://laravel.com/docs/10.x/pint), or [StyleCI](https://styleci.io/)
installed in your project, your stencil will be formatted using that. 
Otherwise, your stencil will not be formatted, unless you provided a custom formatter via the [setFormatter](#setFormatter) option.

:::

### SetFormatter
The **`setFormatter`** method can be used to override the formatter autodiscovery process

```php
Stencil::make()
->setFormatter(function(string $path) {
    // Pass the $path to whatever formatter process you would like
})
->save('/my/path/file.txt')
```

### DisableFormat
The **`disableFormat`** method will disable external formatting altogether.

```php
Stencil::make()
->disableFormat()
->save('/my/path/file.txt')
```

### ToString
The **`__toString`** method will return your fully rendered code stencil

```php
$stencilContent = Stencil::make()
->line('Hello World!')
->__toString()

// Or using a cast

$stencilContent = (string) Stencil::make()
->line('Hello World!')
```
