<?php

namespace App\Domains\MessageModule\Models;

use App\Domains\ConversationModule\Models\Conversation;
use App\Domains\UserModule\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Message extends Model
{

    use HasFactory, SoftDeletes;

    
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($message) {
            // Delete related group_members entries
            DB::table('message_user')->where('message_id', $message->id)->delete();
        });
    }
    protected $fillable = [
        'conversation_id', 'sender_id', 'type', 'content', 'url'
    ];


    // Relationships
    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
