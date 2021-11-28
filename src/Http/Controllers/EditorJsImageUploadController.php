<?php

namespace Broutard\NovaEditorJs\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;
use Spatie\Image\Exceptions\InvalidManipulation;
use Spatie\Image\Image;

class EditorJsImageUploadController extends Controller
{
    /**
     * Upload file
     *
     * @param NovaRequest $request
     * @return array
     */
    public function file(NovaRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image',
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $path = $request->file('image')->store(
            config('nova-editor-js.toolSettings.image.path'),
            config('nova-editor-js.toolSettings.image.disk')
        );

        return [
            'success' => 1,
            'file' => [
                'path' => $path,
                'url' => Storage::disk(config('nova-editor-js.toolSettings.image.disk'))->url($path),
            ]
        ];
    }

    /**
     * @param NovaRequest $request
     * @return array
     */
    public function url(NovaRequest $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    $imageDetails = getimagesize($value);

                    if (!in_array($imageDetails['mime'] ?? '', [
                        'image/jpeg',
                        'image/webp',
                        'image/gif',
                        'image/png',
                        'image/svg+xml',
                    ])) {
                        $fail($attribute . ' is invalid.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return [
                'success' => 0
            ];
        }

        $url = $request->input('url');
        $imageContents = file_get_contents($url);
        $name = parse_url(substr($url, strrpos($url, '/') + 1))['path'];
        $nameWithPath = config('nova-editor-js.toolSettings.image.path') . '/' . uniqid() . $name;

        Storage::disk(config('nova-editor-js.toolSettings.image.disk'))->put($nameWithPath, $imageContents);

        return [
            'success' => 1,
            'file' => [
                'path' => $nameWithPath,
                'url' => Storage::disk(config('nova-editor-js.toolSettings.image.disk'))->url($nameWithPath)
            ]
        ];
    }

}
