## Render flow extension for Laravel Blade

This package adds a several blade directives that lets you reuse a parts of templates.

### Install

Require this package with composer:


```bash
composer require pustato/laravel-blade-render-flow
```

Add the ServiceProvider to the `providers` section in your `config/app.php`:

```php
Pustato\LaravelBladeRenderFlow\ServiceProvider::class,
```

### Usage

#### `@capture`

Lets you render a section of template once and reuse it later.

- `@capture(block_name)` starts capturing block of template with name `block_name`.
- `@endcapture` ends block.
- `@flushcapture` ends block and instantly renders it.
- `@flush(block_name)` render stored block.
- `@clearcapture([block_name])` if `block_name` is specified, forgets block with this name, else - forgets all stored blocks.
 
Example:

```php
@php
    $src = 'http://placehold.it/100x100';
    $title = 'Placeholder image';
@endphp
@capture(placeholder)
<img src="{{ $src }}" title="{{ $title }}" alt="{{ $titel }}"/>
@endcapture

<div>
    @flush('placeholder')
</div>
<div>
    @flush('placeholder')
</div>
<div>
    @flush('placeholder')
</div>
@clearcapture('placeholder')
```

Will be rendered to:

```html
<div>
    <img src="http://placehold.it/100x100" title="Placeholder image" alt="Placeholder image"/>
</div>
<div>
    <img src="http://placehold.it/100x100" title="Placeholder image" alt="Placeholder image"/>
</div>
<div>
    <img src="http://placehold.it/100x100" title="Placeholder image" alt="Placeholder image"/>
</div>
```

#### `@template`

Lets you specify a section of sub template to store it in a memory.

- `@templage(name)` starts sub template with `name`.
- `@endtemplate` ends sub template.
- `@render(name[, params])` render a sub template with specified params (works like `@include`).

Example:

```php
@template('img')
<img src="{{ $src or 'http://placehold.it/100x100' }}" alt="{{ $title or 'Image alt' }}" title="{{ $title or 'Image title' }}"/>
@endtemplate

<div>
    @render('img')
</div>
<div>
    @render('img', ['src' => 'http://placehold.it/350x350', 'title' => 'Large placeholder'])
</div>
<div>
    @render('img', ['src' => 'http://placehold.it/32x32', 'title' => 'Tiny placeholder'])
</div>
```

Result:

```html
<div>
    <img src="http://placehold.it/100x100" alt="Image alt" title="Image title">
</div>
<div>
    <img src="http://placehold.it/350x350" alt="Large placeholder" title="Large placeholder">
</div>
<div>
    <img src="http://placehold.it/32x32" alt="Tiny placeholder" title="Tiny placeholder">
</div>
```
