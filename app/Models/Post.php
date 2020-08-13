<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'page_id', 'color', 'family', 'textSize', 'message', 'youtube', 'image', 'video', 'commentary', 'views'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }
}
