<?php

namespace OpenAdmin\Admin\PageDesigner\Traits;

use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Layout\Content;

//addBodyClass
trait HideNavigation
{
    public function __construct()
    {
        Admin::css('/vendor/open-admin-ext/page-designer/css/page-designer-modal.css');
    }
    /**
         * Index interface.
         *
         * @param Content $content
         *
         * @return Content
         */
    public function index(Content $content)
    {
        $content->addBodyClass("hide-nav  white-bg");
        return parent::index($content);
    }

    /**
     * Show interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function show($id, Content $content)
    {
        $content->addBodyClass("hide-nav  white-bg");
        return parent::show($content);
    }

    /**
     * Edit interface.
     *
     * @param mixed   $id
     * @param Content $content
     *
     * @return Content
     */
    public function edit($id, Content $content)
    {
        $content->addBodyClass("hide-nav  white-bg");
        return parent::edit($content);
    }

    /**
     * Create interface.
     *
     * @param Content $content
     *
     * @return Content
     */
    public function create(Content $content)
    {
        $content->addBodyClass("hide-nav  white-bg");
        return parent::create($content);
    }
}
