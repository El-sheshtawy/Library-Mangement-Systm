<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description'];

    // Define role names as constants
    public const ROLE_SUPER_ADMIN = 1;
    public const ROLE_EDITOR = 2;
    public const ROLE_USER = 3;
    public const ROLE_GUEST = 4;

    // Users with this role
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Permissions associated with this role
    public function permissions()
    {
        return $this->belongsToMany(Permission::class);
    }
}
