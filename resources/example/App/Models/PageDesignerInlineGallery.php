<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageDesignerInlineGallery extends Model
{
    protected $table = 'page_designer_inline_gallery';

    protected $casts = [
        'images' => 'json',
    ];
}
