<?php

namespace OpenAdmin\Admin\PageDesigner;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;

class PageDesignerController extends Controller
{
    public function __construct()
    {
        $this->pageDesigner = new PageDesigner();
    }

    public function index(Request $request, $id = null)
    {
        return Admin::content(function (Content $content) use ($request) {
            $this->pageDesigner->init($request->page_designer_id);
            $this->pageDesigner->setData();

            $content->body(view('open-admin-page-designer::index', $this->pageDesigner->getViewData()));
            $content->header('Page designer');
        });
    }

    public function save(Request $request)
    {
        $config = $this->pageDesigner->config;

        $modelObj = $config['model']::find($request->page_designer_id);
        $modelObj->setAttribute($config['field'], $request->input($config['field']));
        $modelObj->save();

        admin_toastr('Save succeded!', 'success');

        return redirect('/admin/page-designer?page_designer_id='.$request->page_designer_id);
    }
}
