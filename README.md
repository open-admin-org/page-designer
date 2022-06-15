PageDesigner for Open-admin
============================
Freely position items on a page and edit their content.

[![StyleCI](https://styleci.io/repos/503893269/shield?branch=main)](https://styleci.io/repos/503893269)
[![Packagist](https://img.shields.io/github/license/open-admin-org/page-designer.svg?style=flat-square&color=brightgreen)](https://packagist.org/packages/open-admin-ext/page-designer)
[![Total Downloads](https://img.shields.io/packagist/dt/open-admin-ext/page-designer.svg?style=flat-square)](https://packagist.org/packages/open-admin-ext/page-designer)
[![Pull request welcome](https://img.shields.io/badge/pr-welcome-green.svg?style=flat-square)]()


## Screenshot

![extention-page-designer](https://user-images.githubusercontent.com/86517067/173943033-9234fe5a-273f-4383-84a1-d2f38120c3bf.png)


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
$router->resource('page-designer', PageDesignerController::class);
$router->resource('page-designer-images', PageDesignerImagesController::class);
$router->resource('page-designer-videos', PageDesignerVideoController::class);
$router->resource('page-designer-texts', PageDesignerTextController::class);
$router->resource('page-designer-inline-galleries', PageDesignerInlineGalleryController::class);
$router->resource('page-designer-embeds', PageDesignerEmbedController::class);
```

Add front-end routes in routes/web.php
```php
use App\Http\Controllers\PageDesignerController;

Route::get('/page/{id}', [PageDesignerController::class, 'index'])->name('page-designer');
```

### 3) Check it out
App\Controllers\PageDesignerController.php

Now in the form section you see.
```php
$form->pagedesigner('data', __('pageDesign'));
```

##### For the front-end:
Go to your-app-url/page/{id} and see how it look
you can alter the look by changing resources/views/page_designer.blade.php


## Options
```php
// set snap
$form->pagedesigner('data', __('pageDesign'))->snap(30);
```

## Adding new items to the PageDesigner
The PageDesigner automaticly scans the `Admin/Controllers` directory to see if there are controller with `PageDesignItem` trait.

You can generate an AdminController like you normaly would (Tip: use the helper plugin), then you can add the following code below.

Lets say you like an quote item to add to your PageDesigner after you create the controller. Alter it to look like this:

```php
use OpenAdmin\Admin\PageDesigner\Traits\PageDesignItem;

class PageDesignerQuoteController extends AdminController
{
    use PageDesignItem;

    public function __construct()
    {
        $this->initPageDesignItem();
    }

    public static function pageDesign()
    {
        return [
            'parent_field'=> 'page_id',
            'type'        => 'quote',
            'title'       => 'quote',
            'icon'        => 'icon-quote-right',
            'model'       => "\App\Models\PageDesignerQuote",
        ];
    }

    // this part renders the content part of the item
    // the rendering on the frond-end is seperate, but offcourse you can let them share styles
    public static function pageDesignScripts()
    {
        return <<<'JS'
            // use the type as writen above + 'SetContent'
            window.quoteSetContent = function(data,current_content){
                current_content.innerHTML = '<h2>Quote: '+data.quote+'</h2>';
            };
        JS;
    }

    ...
```


License
------------
Licensed under [The MIT License (MIT)](LICENSE).
