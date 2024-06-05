<?php


namespace App\Domains\MessageModule\Models;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    use HasFactory;
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
