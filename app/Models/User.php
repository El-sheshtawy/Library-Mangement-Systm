<?php

namespace App\Models;

use App\Traits\HasImage;
use App\Traits\HasSingleImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasImage;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'token',
        'token_expiration',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    /**
     * Register media collections for User.
     */
    public function registerMediaCollections(): void
    {
        // Profile Image
        $this->registerImageCollection(
            'profile_image',
            true,
            url('/assets/images/static/user.png'),
            public_path('/assets/images/static/user.png')
        );
    }

    /**
     * Accessor for image URL.
     */
    public function getImageUrlAttribute(): string
    {
        return $this->getImageUrl('profile_image');
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            $user->clearMediaCollection('image');
        });
    }

    // Role relationship
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    // Permissions directly assigned to the user
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'user_permission');
    }

    // Check if user has a specific permission
    public function hasPermission($permission)
    {
        // Check direct permissions
        if ($this->permissions()->where('name', $permission)->exists()) {
            return true;
        }

        // Check permissions via role
        return $this->role->permissions()->where('name', $permission)->exists();
    }

    /**
     * Scope a query to search users by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $searchTerm
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByName($query, $searchTerm)
    {
        return $query->where('name', 'LIKE', '%' . $searchTerm . '%');
    }

    /**
     * Scope a query to filter users by role.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $roleId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByRole($query, $roleId)
    {
        return $query->where('role_id', $roleId);
    }

    public function books()
    {
        return $this->hasMany(Book::class);
    }

    public  function authorRequest()
    {
        return $this->hasMany(AuthorRequest::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function downloads()
    {
        return $this->hasMany(Download::class);
    }

}
