<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'name', 'avatar', 'link', 'about', 'views'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followers()
    {
        return $this->hasMany(Following::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class)->orderByDesc('id');
    }

    public function denounced()
    {
        return $this->belongsTo(Denounce::class);
    }
}
