<?php

declare(strict_types=1);

namespace Tests\Unit;

require_once __DIR__ . '/../resources/TestModel.php';

use Illuminate\Database\Eloquent\Model;
use TestModel;
use Tests\TestCase;
use Broutard\NovaEditorJs\EditorJs;

class ModelTest extends TestCase
{
    /**
     * Path to JSON file with valid contents
     */
    private const TEST_FILE_JSON = __DIR__ . '/../resources/json/editorjs.json';

    /**
     * Attribute casting remove self app domain in links.
     *
     * @return void
     */
    public function testCast(): void
    {
        $json = file_get_contents(self::TEST_FILE_JSON);

        $model = new TestModel();
        $model->content = $json;
        $this->assertInstanceOf(EditorJs::class, $model->content);

        // get
        $content = $model->content;
        $this->assertInstanceOf(EditorJs::class, $content);

        // after second set()
        $model->content = $json;
        $this->assertInstanceOf(EditorJs::class, $model->content);
    }

    public function testSerialize(): void
    {
        $model = new TestModel();
        $model->content = file_get_contents(self::TEST_FILE_JSON);

        // toArray()
        $array = $model->toArray();
        $this->assertIsArray($array['content']);

        // toJson()
        $array = json_decode($model->toJson(), true);
        $this->assertIsArray($array['content']);
    }
}
