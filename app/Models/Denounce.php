<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Denounce extends Model
{
    protected $fillable = [
        'page_id', 'user_id', 'denouncer_id', 'denounce'
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function denouncer()
    {
        return $this->belongsTo(User::class, 'denouncer_id');
    }
}
