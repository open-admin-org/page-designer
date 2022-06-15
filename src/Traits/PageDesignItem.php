<?php

namespace OpenAdmin\Admin\PageDesigner\Traits;

use OpenAdmin\Admin\Facades\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\Layout\Content;

//addBodyClass
trait PageDesignItem
{
    public function initPageDesignItem()
    {
        $this->hook("alterForm", function ($scope, $form) {
            Admin::css('/vendor/open-admin-ext/page-designer/css/page-designer-modal.css', false);
            $form = $this->addPageDesigner($form);
            return $form;
        });
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
        return parent::edit($id, $content);
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
        $content->addBodyClass("hide-nav white-bg");
        return parent::create($content);
    }

    public function addPageDesigner($form)
    {
        $settings = self::pageDesign();

        $form->hidden($settings['parent_field'], 'parent')
             ->value(request()->input($settings['parent_field']));

        $form->saved(function (Form $form) {
            $model = $form->model();
            return response('<script>window.top.updateItem('.json_encode($model).');</script>');
        });
        return $form;
    }
}
