---
title: Content Controls
sidebar_position: 3
---

<a class="anchor" name="line"></a>

### Line
The **`line`** method will add content to your stencil on a new line

```php
Stencil::make()
->line('Hello')
->line('World!')
// --- results in --- // 
"Hello
World!
"
```

### Append
The **`append`** method will append content to the stencil on the last line

```php
Stencil::make()
->line('Hello')
->append(' World!')
// --- results in --- // 
"Hello World!
"
```

### Newline
The <strong><code>newline</code></strong> method will add empty line(s) to the stencil

```php
Stencil::make()
->line('Hello')
->newline()
->line('World!')
// --- results in --- // 
"Hello

World!
"
```

### WithoutNewline
The **`withoutNewline`** method will remove the last newline, allowing you to continue your stencil on the same line.
This is useful when working with braces.

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

:::info

The following functions can be used anywhere within the stencil, and will be inserted in the proper location when your stencil is rendered

:::


### Shebang
The **`shebang`** method will add a shebang line to the stencil

```php
Stencil::make()
->line('Hello')
->shebang('<?php')
// --- results in --- // 
"<?php
Hello
"
```

### Php
The **`php`** method is an alias for a php shebang line

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

### StrictTypes
The **`strictTypes`** method will add a strict types declaration to the file

```php
Stencil::make()
->strictTypes()
// --- results in --- // 
"declare(strict_types = 1);
"
```

### Namespace
The **`namespace`** method will set the namespace for the php file

```php
Stencil::make()
->namespace('App/Models')
// --- results in --- // 
"namespace App/Models;
"
```

### Use
The **`use`** method add a use statement for the class at the top of the file.

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
