<?php

namespace OpenAdmin\Admin\PageDesigner;

use Illuminate\Support\ServiceProvider;

class PageDesignerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-admin-page-designer');

        PageDesignerExtention::boot();
    }
}
