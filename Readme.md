# Laravel Nova Editor JS Field

A Laravel Nova implementation of [Editor.js](https://github.com/codex-team/editor.js).  
Forked from https://github.com/advoor/nova-editor-js (thanks to him).

## Installation

Install via composer:

```
composer require broutard/nova-editor-js
```

Publish the config file, langs and views
```
php artisan vendor:publish --provider="Broutard\NovaEditorJs\FieldServiceProvider"
```

## Usage:

Add this cast to your model attributes:

```php
use Broutard\NovaEditorJs\Casts\EditorCast;

protected $casts = [
    'content' => EditorCast::class,
];
```

Use the field in your nova resource file as below:

```php
use Broutard\NovaEditorJs\NovaEditorJs;

NovaEditorJs::make('content');
```

And boom!

You can configure what tools the Editor should use in the config 
file along with some other settings so make sure to have a look :)

You can use the built in function to generate the response for the frontend:

```php
$model->content->render();
```
Each block has it's own view which can be overwritten in `resources/views/vendor/nova-editor-js/`

By default, the `render()` method will use the default published templates and produce classical HTML.  

But you can specify others outputs (like "AMP"), by adding the corresponding templates in `resources/views/vendor/nova-editor-js/amp` and calling `render('amp')`.


## Tools included
* https://github.com/editor-js/code
* https://github.com/editor-js/delimiter
* https://github.com/editor-js/embed
* https://github.com/editor-js/header
* https://github.com/editor-js/image
* https://github.com/editor-js/inline-code
* https://github.com/editor-js/list
* https://github.com/editor-js/marker
* https://github.com/editor-js/raw
* https://github.com/editor-js/table
* https://github.com/editor-js/quote  

## Localization

EditorJs handle [localization natively](https://editorjs.io/internationalization).  
So, a localized JSON file can be used to translate all EditorJs labels (see in your `resources/lang/vendor/nova-editor-js/` folder).

## Extending

**For the purpose of this section, we will use `editor-js/warning` as an example of extensibility.**

There are two steps to extending the editor. The first consists of creating a JavaScript file and passing it onto Nova.
The second step allows you to create a blade view file and pass it to the field to allow your block to render in the Nova `show` page.
 
### Creating the Javascript file

`resources/js/editor-js-plugins/warning.js`

```js
/*
 * The editorConfig variable is used by you to add your tools,
 * or any additional configuration you might want to add to the editor.
 *
 * The fieldConfig variable is the VueJS field exposed to you. You may
 * fetch any value that is contained in your laravel config file from there.
 */
NovaEditorJS.booting(function (editorConfig, fieldConfig) {
    if (fieldConfig.toolSettings.warning.activated === true) {
        editorConfig.tools.warning = {
            class: require('@editorjs/warning'),
            shortcut: fieldConfig.toolSettings.warning.shortcut,
            config: {
                titlePlaceholder: fieldConfig.toolSettings.warning.titlePlaceholder,
                messagePlaceholder: fieldConfig.toolSettings.warning.messagePlaceholder,
            },
        }
    }
});
```

`webpack.mix.js`

```js
const mix = require('laravel-mix');

mix.js('resources/js/editor-js-plugins/warning.js', 'public/js/editor-js-plugins/warning.js');
```

`app/Providers/NovaServiceProvider.php`

```php
// ...
public function boot()
{
    parent::boot();

    Nova::serving(function () {
        Nova::script('editor-js-warning', public_path('js/editor-js-plugins/warning.js'));
    });
}
// ...
```

`config/nova-editor-js.php`

```php
return [
    // ...
    'toolSettings' => [
        'warning' => [
            'activated' => true,
            'titlePlaceholder' => 'Title',
            'messagePlaceholder' => 'Message',
            'shortcut' => 'CMD+SHIFT+L'
        ],
    ]
    // ...
];
```

### Creating the blade view file

`resources/views/editorjs/warning.blade.php`

*CSS classes taken from [here](https://github.com/editor-js/warning/blob/master/src/index.css).*

```html
<div class="editor-js-block">
    <div class="cdx-warning">
        <h3 class="cdx-warning__title">{{ $title }}</h3>
        <p class="cdx-warning__message">{{ $message }}</p>
    </div>
</div>
```

`app/Providers/NovaServiceProvider.php`

```php
use Broutard\NovaEditorJs\EditorJs;

// ...
public function boot()
{
    parent::boot();

    EditorJs::addRender('warning', function($block, $format) {
        return view('editorjs.warning', $block['data'])->render();
    });
    
    // ...
}
// ...
```

That's it for extending the Nova EditorJS package!

