---
title: Content Controls
sidebar_position: 3
---

<a class="anchor" name="line"></a>

<FeatureHeader anchor="line">
The <strong><code>line</code></strong> method will add content to your stencil on a new line
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->line('World!')
// --- results in --- // 
"Hello
World!
"
```

<FeatureHeader anchor="append">
The <strong><code>append</code></strong> method will append content to the stencil on the last line
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->line(' World!')
// --- results in --- // 
"Hello World!
"
```

<FeatureHeader anchor="newline">
The <strong><code>newline</code></strong> method will add empty line(s) to the stencil
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->newline()
->line(' World!')
// --- results in --- // 
"Hello

World!
"
```

<FeatureHeader anchor="withoutNewline">
The <strong><code>withoutNewline</code></strong> method will remove the last newline, allowing you to continue your stencil on the same line.
This is useful when working with braces.
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->withoutNewline()
->line(' World!')
// --- results in --- // 
"Hello World!
"
```

---

:::note

The following functions can be used anywhere within the stencil, and will be inserted in the proper location when your stencil is rendered

:::

<FeatureHeader anchor="shebang">
The <strong><code>shebang</code></strong> method will add a shebang line to the stencil
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->shebang('<?php')
// --- results in --- // 
"<?php
Hello
"
```

<FeatureHeader anchor="php">
The <strong><code>php</code></strong> method is an alias for a php shebang line
</FeatureHeader>

```php
Stencil::make()
->line('Hello')
->php()
```

```php
// --- results in --- // 
"<?php
Hello
"
```

<FeatureHeader anchor="strictTypes">
The <strong><code>strictTypes</code></strong> method will add a strict types declaration to the file
</FeatureHeader>

```php
Stencil::make()
->strictTypes()
// --- results in --- // 
"declare(strict_types = 1);
"
```

<FeatureHeader anchor="namespace">
The <strong><code>namespace</code></strong> method will set the namespace for the php file
</FeatureHeader>

```php
Stencil::make()
->namespace('App/Models')
// --- results in --- // 
"namespace App/Models;
"
```

<FeatureHeader anchor="use">
The <strong><code>use</code></strong> method will set the namespace for the php file.
</FeatureHeader>

Uses can be added from anywhere in the stencil, loops, conditionals, function calls, or anywhere else, 
and will always show up in the proper location in the stencil

```php
Stencil::make()
->php()
->namespace('App/Models')
->use(MyClass::class)
// --- results in --- // 
"namespace App/Models;

use App/Models/MyClass;
"
```
