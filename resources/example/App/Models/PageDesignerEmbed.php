<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageDesignerEmbed extends Model
{
    protected $table = 'page_designer_embed';

    protected $casts = [
        'embed_data' => 'json',
    ];
}
