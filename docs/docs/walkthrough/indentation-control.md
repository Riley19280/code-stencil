---
title: Indentation Control
sidebar_position: 4
---

Although running your projects code style tooling after generating code is recommended, some basic indentation controls are also provided.

### Indent
The **`indent`** method will increase the indent level of the code

### Deindent
The **`deindent`** method will decrease the indent level of the code

```php
Stencil::make()
->newline()
->indent()
->line('Hello')
->line('World')
->deintent()
->line('Tada!')
// --- results in --- // 
"
••••Hello
••••World
Tada!
"
```

### Indented
The **`indented`** method will indent the content passed, and then return the indent level to the previous level

```php
Stencil::make()
->newline()
->indented('Hello')
->line('World')
// --- results in --- // 
"
••••Hello
World
"
```

### SetIndentLevel
The **`setIndentLevel`** method is an alias for a php shebang line

```php
Stencil::make()
->line('Hello')
->setIndentLevel(2)
->line('World')
// --- results in --- // 
"Hello
••••••••World
"
```

### SpacesPerIndent
The **`spacesPerIndent`** changes the number of spaces when the <code>indent</code> method is used

```php
Stencil::make()
->newline()
->spacesPerIndent(2)
->indent()
->line('Hello')
// --- results in --- // 
"
••Hello
"
```
