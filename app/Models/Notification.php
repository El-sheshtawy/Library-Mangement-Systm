<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected  $fillable = [
        'title',
        'content',
        'type',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->using(NotificationUser::class)
                    ->withPivot('is_read', 'is_public', 'receiver_id') 
                    ->withTimestamps();
    }
}
