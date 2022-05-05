<?php

namespace App\Models;

use App\Models\Chat;
use App\Models\Message;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    # UN USUARIO TIENE MUCHOS CHATS
    public function chats()
    {
        // return $this->belongsToMany('App\Models\Chat');
        return $this->belongsToMany(Chat::class);
    }

    # UN USUARIOS TIENE MUCHOS MENSAJES
    public function messages()
    {
        // return $this->hasMany('App\Models\Message');
        return $this->hasMany(Message::class);
    }
}
