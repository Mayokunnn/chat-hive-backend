<?php

namespace App\Domains\UserModule\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoginAttempt extends Model
{
    protected $fillable = ['user_id', 'attempts', 'last_attempt_at'];

    /**
     * Get the user that owns the login attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }    
}
