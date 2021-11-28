<?php

namespace Tests;

use Broutard\NovaEditorJs\FieldServiceProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

/**
 * Defines basic system to test with
 */
class TestCase extends OrchestraTestCase
{
    /**
     * Path to config file from here
     */
    private const CONFIG_PATH = __DIR__ . '/../config/nova-editor-js.php';

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [FieldServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('nova-editor-js', require self::CONFIG_PATH);
    }
}
