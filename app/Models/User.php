<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = [
        'password',
    ];

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    public function groupMemberships()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function groups()
    {
        return $this->hasManyThrough(Group::class, GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
}
