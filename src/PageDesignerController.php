<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;
use OpenAdmin\Admin\PageDesigner\PageDesigner;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Models\Page;

class PageDesignerController extends Controller
{
    public function index(Request $request, $id = null)
    {
        return Admin::content(function (Content $content) use ($id, $request) {
            $this->page_id = $request->get('page_id');
            $page = Page::find($this->page_id);

            $pageDesigner = new PageDesigner($this->page_id);

            $view_data = $pageDesigner->getViewData();
            $view_data['page_id']   = $this->page_id;
            $view_data['page_data'] = $page->data;

            $content->body(view('laravel-admin-page-designer::index', $view_data));

            $content->header("Page designer");
        });
    }

    public function save(Request $request)
    {
        $page = Page::find($request->page_id);
        $page->data = $request->data;
        $page->save();

        admin_toastr('Message...', 'success');

        return redirect("/admin/page-designer?page_id=".$request->page_id);
    }
}
