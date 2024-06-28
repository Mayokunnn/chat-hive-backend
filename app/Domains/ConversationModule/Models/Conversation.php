<?php

namespace App\Domains\ConversationModule\Models;

use App\Domains\MessageModule\Models\Message;
use App\Domains\UserModule\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Conversation extends Model
{
    use HasFactory, SoftDeletes;


    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($conversation) {
            // Delete related conversation_user entries
            DB::table('conversation_user')->where('conversation_id', $conversation->id)->delete();
        });
    }
    protected $fillable = ['name', 'image'];

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
