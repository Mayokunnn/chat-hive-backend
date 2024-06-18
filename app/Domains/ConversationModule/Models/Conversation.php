<?php

namespace App\Domains\ConversationModule\Models;

use App\Domains\MessageModule\Models\Message;
use App\Domains\UserModule\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    use HasFactory;
    protected $fillable = [];

    public function users()
    {
        return $this->belongsToMany(User::class, 'conversation_user', 'conversation_id', 'user_id');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function group()
    {
        return $this->hasOne(Group::class);
    }
}
