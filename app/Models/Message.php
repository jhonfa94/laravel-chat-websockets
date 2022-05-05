<?php

namespace App\Models;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'chat_id',
        'content'
    ];

    # UN MENSAJE PERTENECE A UN UUSARIO
    public function user()
    {
        // return $this->belongsTo('App\Models\User');
        return $this->belongsTo(User::class);
    }

    # UN MENSAJE PERTENECE A UN CHAT
    public function chat()
    {
        // return $this->belongsTo('App\Models\Chat');
        return $this->belongsTo(Chat::class);
    }
}
