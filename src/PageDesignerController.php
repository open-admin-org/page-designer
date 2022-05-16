<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PageDesignerController extends Controller
{
    public function index(Request $request, $id = null)
    {
        return Admin::content(function (Content $content) use ($id, $request) {
            // $offset = $request->get('offset');

            $viewer = new PageDesigner($id);

            $content->body(view('laravel-admin-page-designer::index', [
                'test'      => 'test',
            ]));

            $content->header("Page designer");
        });
    }
}
