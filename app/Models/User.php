<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'remember_token', 'role', 'pro', 'document', 'punished_at', 'punished_days'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function following()
    {
        return $this->hasMany(Following::class);
    }

    public function denounces()
    {
        return $this->hasMany(Denounce::class, 'denouncer_id');
    }

    public function denounced()
    {
        return $this->belongsTo(Denounce::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
