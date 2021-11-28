<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use Broutard\NovaEditorJs\EditorJs;

class NovaFieldTest extends TestCase
{
    /**
     * Path to JSON file with valid contents
     */
    private const TEST_FILE_JSON_WITH_LINK = __DIR__ . '/../resources/json/editorjs_with_link.json';
    private const TEST_FILE_JSON_WITH_IMAGE = __DIR__ . '/../resources/json/editorjs_with_image.json';

    /**
     * Attribute casting remove self app domain in links.
     *
     * @return void
     */
    public function testUrlIsSanitized(): void
    {
        // Get contents
        $json = file_get_contents(self::TEST_FILE_JSON_WITH_LINK);

        Config::set('app.url', 'http://www.testdomain.com');

        $content = (new EditorJs($json))->sanitize()->json();

        // self app domain should be removed
        $this->assertStringNotContainsString('www.testdomain.com', (string)$content);
        // others should not
        $this->assertStringContainsString('www.test.com', (string)$content);
    }

    /**
     * Attribute casting remove image url.
     *
     * @return void
     */
    public function testImageIsStoreWithoutUrl(): void
    {
        // Get contents
        $json = file_get_contents(self::TEST_FILE_JSON_WITH_IMAGE);

        $content = (new EditorJs($json))->sanitize();

        // set content without url
        $this->assertNotEmpty(Arr::get($content->toArray(), 'blocks.0.data.file'));
        $this->assertEmpty(Arr::get($content->toArray(), 'blocks.0.data.file.url'));

        // get content with url (for editor)
        $contentRetrieved = json_decode($content->resolveForEditor(), true);

        $this->assertNotEmpty(Arr::get($contentRetrieved, 'blocks.0.data.file.url'));
    }
}
