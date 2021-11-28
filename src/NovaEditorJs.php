<?php

namespace Broutard\NovaEditorJs;

use Closure;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Http\Requests\NovaRequest;
use Broutard\NovaEditorJs\Rules\IsValid;

class NovaEditorJs extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'nova-editor-js';

    /**
     * @var EditorJs
     */
    protected $editor;

    public function __construct($name, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name, $attribute, $resolveCallback);

        $translations = $this->loadTranslations(resource_path('lang/vendor/nova-editor-js/' .  app()->getLocale() . '.json'));

        $this->withMeta([
            'editorSettings'             => array_merge(
                config('nova-editor-js.editorSettings'),
                $translations,
            ),
            'toolSettings'               => config('nova-editor-js.toolSettings'),
            'uploadImageByFileEndpoint'  => route('editor-js-upload-image-by-file'),
            'uploadImageByUrlEndpoint'   => route('editor-js-upload-image-by-url'),
            'searchInternalLinkEndpoint' => route('editor-js-search-internal-link'),
        ]);
    }

    /**
     * Get the validation rules for this field.
     *
     * @param NovaRequest $request
     * @return array
     */
    public function getRules(NovaRequest $request)
    {
        return array_merge_recursive(parent::getRules($request), [
            $this->attribute => [
                new IsValid(),
            ],
        ]);
    }

    /**
     * Resolve the field's value for display.
     *
     * @param mixed $resource
     * @param string|null $attribute
     * @throws \Throwable
     */
    public function resolveForDisplay($resource, $attribute = null)
    {
        $this->resource = $resource;

        $attribute = $attribute ?? $this->attribute;

        if ($attribute === 'ComputedField') {
            return;
        }

        $value = data_get($resource, str_replace('->', '.', $attribute));

        // force cast if attribute is not casted
        if (!$value instanceof EditorJs) {
            $value = new EditorJs($value);
        }

        if (!$this->displayCallback) {
            $this->withMeta(['asHtml' => true]);
            // generate output with package templates ("nova")
            $this->value = '<div class="editor-js-content">' . $value->render('nova') . '</div>';
        } elseif (is_callable($this->displayCallback)) {
            $this->value = call_user_func($this->displayCallback, $value);
        }
    }

    /**
     * Resolve the given attribute from the given resource.
     *
     * @param  mixed  $resource
     * @param  string  $attribute
     * @return mixed
     */
    protected function resolveAttribute($resource, $attribute)
    {
        /** @var EditorJs $value */
        $value = parent::resolveAttribute($resource, $attribute);

        // force cast if attribute is not casted
        if (!$value instanceof EditorJs) {
            $value = new EditorJs($value);
        }

        return $value->resolveForEditor();
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  object  $model
     * @param  string  $attribute
     * @return mixed
     */
    protected function fillAttributeFromRequest(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        if ($request->exists($requestAttribute)) {
            $editor = new EditorJs($request[$requestAttribute]);

            $value = $editor->sanitize()->json();

            $model->{$attribute} = $this->isNullValue($value) ? null : $value;
        }
    }

    /**
     * Load the lang file to inject into EditorJS
     *
     * @param  string  $translations
     */
    protected function loadTranslations($translations)
    {
        if (is_string($translations) && is_readable($translations)) {
            return [
                'i18n' => [
                    'messages' => json_decode(file_get_contents($translations), true)
                ]
            ];
        }

        return [];
    }
}
