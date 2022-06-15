Log viewer for laravel-admin
============================

[![StyleCI](https://styleci.io/repos/503893269/shield?branch=main)](https://styleci.io/repos/503893269)
[![Packagist](https://img.shields.io/github/license/open-admin-org/page-designer.svg?style=flat-square&color=brightgreen)](https://packagist.org/packages/open-admin-ext/page-designer)
[![Total Downloads](https://img.shields.io/packagist/dt/open-admin-ext/page-designer.svg?style=flat-square)](https://packagist.org/packages/open-admin-ext/page-designer)
[![Pull request welcome](https://img.shields.io/badge/pr-welcome-green.svg?style=flat-square)]()

## Screenshot


## Installation

### 1) Terminal

```
$ composer require open-admin-ext/page-designer

$ php artisan admin:import page-designer

$ php artisan vendor:publish --tag=page-designer

$ php artisan migrate
```

### 2) Add Routes
Add admin routes in App/Admin/routes.php
```php
$router->resource('page-designer-images', PageDesignerImagesController::class);
$router->resource('page-designer-videos', PageDesignerVideoController::class);
$router->resource('page-designer-texts', PageDesignerTextController::class);
$router->resource('page-designer-inline-galleries', PageDesignerInlineGalleryController::class);
$router->resource('page-designer-embeds', PageDesignerEmbedController::class);
```

Add admin routes in routes/web.php
```php
use App\Http\Controllers\PageDesignerController;

Route::get('/page/{id}', [PageDesignerController::class, 'index'])->name('page-designer');
```



License
------------
Licensed under [The MIT License (MIT)](LICENSE).
