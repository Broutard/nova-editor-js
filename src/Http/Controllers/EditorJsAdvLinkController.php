<?php

namespace Broutard\NovaEditorJs\Http\Controllers;

use Illuminate\Container\Container;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Laravel\Nova\Http\Requests\NovaRequest;

class EditorJsAdvLinkController extends Controller
{
    /**
     * Search internal links
     *
     * @param NovaRequest $request
     * @return array
     */
    public function search(NovaRequest $request)
    {
        $searchClass = config('nova-editor-js.toolSettings.link.search');

        if (!$searchClass) {
            return abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'A search class should be defined!');
        }

        $validator = Validator::make($request->all(), [
            'search' => 'required',
        ]);

        if ($validator->fails()) {
            return abort(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // forward call to search class
        return Container::getInstance()->call($searchClass, [$request]);
    }
}
