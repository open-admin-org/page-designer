<?php

namespace OpenAdmin\Admin\PageDesigner;

use Illuminate\Support\ServiceProvider;
use OpenAdmin\Admin\Admin;
use OpenAdmin\Admin\Form;
use OpenAdmin\Admin\PageDesigner\PageDesignerField;
use OpenAdmin\Admin\PageDesigner\PageDesignerExtention;

class PageDesignerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        PageDesignerExtention::boot();

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'open-admin-page-designer');

        if ($this->app->runningInConsole()) {
            $this->publishes(
                [
                    __DIR__.'/../resources/assets/'  => public_path('vendor/open-admin-ext/page-designer/'),
                    __DIR__.'/../resources/example/' => base_path('')
                ],
                'page-designer'
            );
        }

        Admin::booting(function () {
            Form::extend('pagedesigner', PageDesignerField::class);
        });
    }
}
