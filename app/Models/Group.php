<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'owner_id', 'conversation_id'
    ];


   // Relationships
   public function owner()
   {
       return $this->belongsTo(User::class, 'owner_id');
   }

   public function members()
   {
       return $this->belongsToMany(User::class, 'group_members', 'group_id', 'user_id');
   }

   public function conversation()
   {
       return $this->belongsTo(Conversation::class);
   }
}
