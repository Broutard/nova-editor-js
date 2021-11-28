<?php

declare(strict_types=1);

namespace Tests\Unit;

use Illuminate\Support\Collection;
use Tests\TestCase;
use Broutard\NovaEditorJs\EditorJs;

class EditorTest extends TestCase
{
    /**
     * Path to JSON file with valid contents
     */
    private const TEST_FILE_JSON = __DIR__ . '/../resources/json/editorjs.json';
    private const TEST_FILE_HTML = __DIR__ . '/../resources/html/editorjs.html';

    /**
     * Returns JSON contents from the file
     *
     * @return string[]
     */
    private function getFileContents(): array
    {
        return [
            'json' => file_get_contents(self::TEST_FILE_JSON),
            'html' => file_get_contents(self::TEST_FILE_HTML)
        ];
    }

    /**
     * Test values
     *
     * @return void
     */
    public function testValues(): void
    {
        // Get contents
        $contents = $this->getFileContents();

        // Verify JSON
        $json = $contents['json'];
        $this->assertIsString($json);

        $editor = new EditorJs($json);

        // Convert to array
        $this->assertIsArray($editor->toArray());

        // Convert to json string
        $this->assertIsString($editor->json());
        $this->assertIsString((string)$editor);
    }

    /**
     * Test rendering
     *
     * @return void
     */
    public function testRender(): void
    {
        // Get contents
        $contents = $this->getFileContents();

        // Verify JSON
        $json = $contents['json'];
        $this->assertIsString($json);

        // Convert to HTML
        $html = (new EditorJs($json))->render('nova');

        // Ensure identicality
        $this->assertEquals($contents['html'], $html);
    }

    /**
     * Test blocks
     *
     * @return void
     */
    public function testBlocks(): void
    {
        // Get contents
        $contents = $this->getFileContents();

        // Get blocks
        $json = $contents['json'];
        $blocks = (new EditorJs($json))->getBlocks();

        $this->assertInstanceOf(Collection::class, $blocks);
        $this->assertEquals(3, count($blocks));
    }
}
