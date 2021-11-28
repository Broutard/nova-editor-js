<?php

namespace Broutard\NovaEditorJs;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Broutard\NovaEditorJs\Exceptions\EditorJSException;

class EditorJs
{
    protected $config;

    protected $value;

    /**
     * render callbacks, used for extending render functionality.
     */
    protected static $renderCallbacks = [];
    protected static $hasBootedRenderCallbacks = false;

    /**
     * sanitize callbacks, used for extending sanitize functionality.
     */
    protected static $sanitizeCallbacks = [];
    protected static $hasBootedSanitizeCallbacks = false;

    public function __construct($value = '')
    {
        $this->config = config('nova-editor-js');

        // if ($value instanceof self) {
        //     $value = $value->value;
        // }

        $this->value = is_string($value) ? json_decode($value, true) : $value;

        if (!empty($value) && !isset($this->value['blocks'])) {
            throw new EditorJSException('EditorJS: value passed to constructor seems not a valid json!');
        }
    }

    /**
     * Return the json representation
     * @return string|null
     */
    public function json(): ?string
    {
        return $this->getBlocks()->isEmpty() ? null : json_encode($this->value);
    }

    /**
     * Return the json representation
     * @return string
     */
    public function __toString()
    {
        return (string)$this->json();
    }

    /**
     * Return the array representation
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this->value;
    }

    /**
     * Get blocks collection
     * @return Collection
     */
    public function getBlocks(): Collection
    {
        return new Collection($this->value['blocks'] ?? []);
    }

    /**
     * Set blocks
     * @param array|Collection $block
     */
    public function setBlocks($blocks)
    {
        $this->value['blocks'] = $blocks instanceof Collection ? $blocks->all() : $blocks;

        // $this->fillForEditor();
    }


    /**
     * Add a custom render callback for the given block.
     *
     * @param          $block
     * @param callable $callback
     */
    public static function addRender($block, callable $callback)
    {
        static::$renderCallbacks[$block] = $callback;
    }

    /**
     * Add a custom sanitizer callback for the given block.
     *
     * @param          $block
     * @param callable $callback
     */
    public static function addSanitizer($block, callable $callback)
    {
        static::$sanitizeCallbacks[$block] = $callback;
    }

    /**
     * Resolve blocks for EditorJs.
     * @return string
     */
    public function resolveForEditor(): string
    {
        $value = $this->value;

        $value['blocks'] = $this->getBlocks()->map(function($block) {
            // EditorJs require file.url to display the image
            if ($block['type'] === 'image' && $path = Arr::get($block, 'data.file.path')) {
                $url = Storage::disk(config('nova-editor-js.toolSettings.image.disk'))->url($path);
                $block['data']['file']['url'] = $url;
            }

            return $block;
        })->toArray();

        return json_encode($value);
    }

    /**
     * Generate output
     * @param string $format
     * @return string
     * @throws EditorJSException
     * @throws \Throwable
     */
    public function render($format = 'HTML'): string
    {
        static::bootRenderCallbacks();

        $output = '';

        foreach ($this->getBlocks() as $block) {
            if (!empty($block['data']) && isset(static::$renderCallbacks[$block['type']])) {
                $output .= html_entity_decode((static::$renderCallbacks[$block['type']])($block, strtolower($format)));
            }
        }

        // handle links
        $output = preg_replace_callback(
            '~<a .*?data-link.*?>.*?</a>~is',
            function ($matches) {
                return app('editorjs.handler')->handleLink($matches[0]);
            },
            $output
        );

        return $output;
    }

    /**
     * Sanitize blocks before save (before inserting into database)
     */
    public function sanitize()
    {
        static::bootSanitizeCallbacks();

        $blocks = $this->getBlocks()->map(function($block) {
            if (!empty($block['data']) && isset(static::$sanitizeCallbacks[$block['type']])) {
                $block = (static::$sanitizeCallbacks[$block['type']])($block);
            }

            return $block;
        });

        $this->value['blocks'] = $blocks->toArray();

        return $this;
    }

    /**
     * Boots the HTML callbacks, as to allow extension
     * of HTML output for custom blocks
     *
     * @return void
     */
    protected static function bootSanitizeCallbacks()
    {
        if (static::$hasBootedSanitizeCallbacks) return;

        static::$sanitizeCallbacks['paragraph'] = static::$sanitizeCallbacks['paragraph'] ?? function ($block) {
            return app('editorjs.sanitizer')->handleLinks($block);
        };

        static::$sanitizeCallbacks['image'] = static::$sanitizeCallbacks['image'] ?? function ($block) {
            return app('editorjs.sanitizer')->handleImage($block);
        };

        static::$hasBootedSanitizeCallbacks = true;
    }

    /**
     * Boots the render callbacks, as to allow extension
     * of output for custom blocks
     *
     * @return void
     */
    protected static function bootRenderCallbacks()
    {
        if (static::$hasBootedRenderCallbacks) return;

        static::$renderCallbacks['header'] = static::$renderCallbacks['header'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.heading", $block['data'])->render();
        };

        static::$renderCallbacks['paragraph'] = static::$renderCallbacks['paragraph'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.paragraph", $block['data'])->render();
        };

        static::$renderCallbacks['list'] = static::$renderCallbacks['list'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.list", $block['data'])->render();
        };

        static::$renderCallbacks['image'] = static::$renderCallbacks['image'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.image", $block['data'])->render();
        };

        static::$renderCallbacks['code'] = static::$renderCallbacks['code'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.code", $block['data'])->render();
        };

        static::$renderCallbacks['delimiter'] = static::$renderCallbacks['delimiter'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.delimiter", $block['data'])->render();
        };

        static::$renderCallbacks['table'] = static::$renderCallbacks['table'] ?? function ($block, $format) {
            if (!isset($block['data']['withHeadings'])) {
                $block['data']['withHeadings'] = false;
            }
            return view("nova-editor-js::{$format}.table", $block['data'])->render();
        };

        static::$renderCallbacks['raw'] = static::$renderCallbacks['raw'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.raw", $block['data'])->render();
        };

        static::$renderCallbacks['embed'] = static::$renderCallbacks['embed'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.embed", $block['data'])->render();
        };

        static::$renderCallbacks['quote'] = static::$renderCallbacks['quote'] ?? function ($block, $format) {
            return view("nova-editor-js::{$format}.quote", $block['data'])->render();
        };

        static::$hasBootedRenderCallbacks = true;
    }
}
