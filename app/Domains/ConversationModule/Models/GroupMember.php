<?php

namespace App\Domains\ConversationModule\Models;

use App\Domains\ConversationModule\Models\Group;
use App\Domains\UserModule\Models\User;
use Illuminate\Database\Eloquent\Model;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id', 'user_id'
    ];

    // Relationships
    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
