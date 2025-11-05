<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
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

    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
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

    public function profile(): HasOne
    {

        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    public function mentors(): BelongsToMany
    {
        return $this->belongsToMany(
            Mentor::class,
            'mentor_user',
            'user_id',
            'mentor_id',
            'user_id',
            'mentor_id'
        );

    }
    public function collaborationsAsRequester(): HasMany
    {
        // 'requester_id' adalah foreign key di tabel 'collaborations'
        // 'user_id' adalah primary key di tabel 'users' (model ini)
        return $this->hasMany(Collaboration::class, 'requester_id', 'user_id');
    }

    public function collaborationsAsRecipient(): HasMany
    {
        // 'recipient_id' adalah foreign key di tabel 'collaborations'
        // 'user_id' adalah primary key di tabel 'users' (model ini)
        return $this->hasMany(Collaboration::class, 'recipient_id', 'user_id');
    }

}
