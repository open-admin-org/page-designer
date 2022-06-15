<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageDesignerImages extends Model
{
    protected $table = 'page_designer_images';

    protected $casts = [
        'images' => 'json',
    ];
}
