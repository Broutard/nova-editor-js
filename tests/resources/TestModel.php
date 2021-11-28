<?php

use Illuminate\Database\Eloquent\Model;
use Broutard\NovaEditorJs\Casts\EditorCast;

class TestModel extends Model
{
    protected $table = 'test';

    public $timestamps = false;

    protected $casts = [
        'content' => EditorCast::class,
    ];
}
