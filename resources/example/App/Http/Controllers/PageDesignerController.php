<?php

namespace App\Http\Controllers;

use App\Models\PageDesigner as PageDesignerModel;
use OpenAdmin\Admin\PageDesigner\PageDesigner;

class PageDesignerController extends Controller
{
    public function index($id = false)
    {
        $page = PageDesignerModel::find($id);

        $pageDesigner = new PageDesigner();
        $pageDesigner->init($page->id);
        $pageDesigner->setData(json_decode($page->data));

        return view('page_designer', $pageDesigner->getViewData(true));
    }
}
