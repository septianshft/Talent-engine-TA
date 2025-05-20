<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        // 'role', // Removed, using roles relationship now
        'phone_number',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->map(fn (string $name) => Str::of($name)->substr(0, 1))
            ->implode('');
    }

    /**
     * The competencies that this user (talent) possesses.
     */
    public function competencies(): BelongsToMany
    {
        return $this->belongsToMany(Competency::class, 'competency_user')
                    ->withPivot('proficiency_level');
    }

    /**
     * The talent requests created by this user.
     */
    public function createdRequests(): HasMany
    {
        return $this->hasMany(TalentRequest::class, 'user_id');
    }

    /**
     * The talent requests assigned to this user (talent) through the pivot table.
     */
    public function assignedRequests(): BelongsToMany
    {
        return $this->belongsToMany(TalentRequest::class, 'talent_request_assignments', 'talent_id', 'talent_request_id')
                    ->withPivot('status') // To get the status of each assignment
                    ->withTimestamps(); // If you want to track when assignments are created/updated
    }

    /**
     * The roles that belong to the user.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

    /**
     * Check if the user has a specific role.
     *
     * @param string $roleName
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }
}
