<?php

namespace OpenAdmin\Admin\PageDesigner;

use OpenAdmin\Admin\Admin;

trait BootExtension
{
    /**
     * {@inheritdoc}
     */
    public static function boot()
    {
        static::registerRoutes();

        Admin::extend('page-designer', __CLASS__);

        Admin::css('/vendor/open-admin-ext/page-designer/css/page-designer.css');
        Admin::css('/vendor/open-admin-ext/page-designer/js/resizeable.css');
        Admin::js('/vendor/open-admin-ext/page-designer/js/resizeable.js');
        Admin::js('/vendor/open-admin-ext/page-designer/js/interact.min.js');
        Admin::js('/vendor/open-admin-ext/page-designer/js/page-designer.js');
        Admin::js('/vendor/open-admin-ext/page-designer/js/page-designer-content.js');
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    protected static function registerRoutes()
    {
        parent::routes(function ($router) {
            /* @var \Illuminate\Routing\Router $router */
            $router->get('page-designer', 'OpenAdmin\Admin\PageDesigner\PageDesignerController@index')->name('page-designer-index');
            $router->post('page-designer/save', 'OpenAdmin\Admin\PageDesigner\PageDesignerController@save')->name('page-designer-file');
        });
    }

    /**
     * {@inheritdoc}
     */
    public static function import()
    {
        parent::createMenu('Page Designer', 'page-designer', 'icon-object-group');

        parent::createPermission('Page designer', 'ext.page-designer', 'page-designer*');
    }
}
