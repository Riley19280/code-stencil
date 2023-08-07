---
title: Saving your stencil
sidebar_position: 5
---

<FeatureHeader anchor="save">
The <strong><code>save</code></strong> method will save your code stencil to a file
</FeatureHeader>

If the provided path does not exist, then it will be created.

```php
Stencil::make()
->line('Hello World!')
->line('World!')
->save('/my/path/file.txt')
```

<FeatureHeader anchor="dryRun">
The <strong><code>dryRun</code></strong> method, indicates that the file should not be saved to the disk. 
</FeatureHeader>

```php
Stencil::make()
->line('Hello World!')
->line('World!')
->dryRun()
->save('/my/path/file.txt')
// Output file will not be created
```

---

Code Stencils provide a variety of ways to format your code after its generated. 
By default, if you have [PHP-CS-Fixer](https://github.com/PHP-CS-Fixer/PHP-CS-Fixer), [Pint](https://laravel.com/docs/10.x/pint), or [StyleCI](https://styleci.io/)
installed in your project, your stencil will be formatted using that. 
Otherwise, your stencil will not be formatted, unless you provided a custom formatter via the [setFormatter](#setFormatter) option.

<FeatureHeader anchor="setFormatter">
The <strong><code>setFormatter</code></strong> method can be used to override the formatter autodiscovery process
</FeatureHeader>

```php
Stencil::make()
->setFormatter(function(string $path) {
    // Pass the $path to whatever formatter process you would like
})
->save('/my/path/file.txt')
```

<FeatureHeader anchor="disableFormat">
The <strong><code>disableFormat</code></strong> method will disable external formatting altogether.
</FeatureHeader>

```php
Stencil::make()
->disableFormat()
->save('/my/path/file.txt')
```

<FeatureHeader anchor="toString">
The <strong><code>__toString</code></strong> method will return your fully rendered code stencil
</FeatureHeader>

```php
$stencilContent = Stencil::make()
->line('Hello World!')
->__toString()

// Or using a cast

$stencilContent = (string) Stencil::make()
->line('Hello World!')
```
