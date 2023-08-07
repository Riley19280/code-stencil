---
title: Indentation Control
sidebar_position: 4
---

Although running your projects code style tooling after generating code is recommended, some basic indentation controls are also provided.


<FeatureHeader anchor="indent">
The <strong><code>indent</code></strong> method is an alias for a php shebang line
</FeatureHeader>

<FeatureHeader anchor="deindent">
The <strong><code>deindent</code></strong> method is an alias for a php shebang line
</FeatureHeader>

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

<FeatureHeader anchor="indented">
The <strong><code>indented</code></strong> will indent the content passed, and then return the indent level to the previous level
</FeatureHeader>

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

<FeatureHeader anchor="setIndentLevel">
The <strong><code>setIndentLevel</code></strong> method is an alias for a php shebang line
</FeatureHeader>

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

<FeatureHeader anchor="spacesPerIndent">
The <strong><code>spacesPerIndent</code></strong> changes the number of spaces when the <code>indent</code> method is used
</FeatureHeader>

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
