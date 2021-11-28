<?php

use Illuminate\Support\Facades\Route;

use \Broutard\NovaEditorJs\Http\Controllers\EditorJsImageUploadController;
use \Broutard\NovaEditorJs\Http\Controllers\EditorJsLinkController;
use \Broutard\NovaEditorJs\Http\Controllers\EditorJsAdvLinkController;

// block/image
Route::post('upload/file', EditorJsImageUploadController::class . '@file')->name('editor-js-upload-image-by-file');
Route::post('upload/url', EditorJsImageUploadController::class . '@url')->name('editor-js-upload-image-by-url');

// paragraph/advlink
Route::get('search/link', EditorJsAdvLinkController::class . '@search')->name('editor-js-search-internal-link');
