<?php

namespace App\Domains\UserModule\Models;

use App\Domains\ConversationModule\Models\Conversation;
use App\Domains\ConversationModule\Models\Group;
use App\Domains\ConversationModule\Models\GroupMember;
use App\Domains\MessageModule\Models\Message;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Auth\Authorizable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JWTSubject
{
    use Authenticatable, Authorizable, HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = [
        'password',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            // Delete related group_members entries
            DB::table('group_members')->where('user_id', $user->id)->delete();
            DB::table('message_user')->where('user_id', $user->id)->delete();
            DB::table('conversation_user')->where('user_id', $user->id)->delete();
        });
    }
    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

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

    public function loginAttempt(): HasOne
    {
        return $this->hasOne(LoginAttempt::class);
    }
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
}
