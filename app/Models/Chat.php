<?php

namespace App\Models;

use App\Models\User;
use App\Models\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{

    use HasFactory;

    // protected $fillable = [
    //     'user_id',
    //     'chat_id'
    // ];

    # UN CHAT PERTENENCE A MUCHOS USUARIOS
    public function users()
    {
        // return $this->belongsToMany('App\Models\User');
        return $this->belongsToMany(User::class);
    }


    # UN CHAT TIENE MUCHOS MENSAJES
    public function messages()
    {
        // return $this->hasMany('App\Models\Message');
        return $this->hasMany(Message::class);
    }
}
