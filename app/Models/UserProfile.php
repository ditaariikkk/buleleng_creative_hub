<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserProfile extends Model
{
    use HasFactory;


    protected $primaryKey = 'profile_id';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'bio',
        'phone_number',
        'portfolio_url',
        'user_photo',
        'category',
        'business_name',
    ];


    public function user(): BelongsTo // 
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function creativeSubSectors(): BelongsToMany
    {
        return $this->belongsToMany(
            CreativeSubSector::class,
            'profile_sub_sector', // Nama tabel pivot
            'user_profile_id',         // Foreign key untuk UserProfile di pivot
            'sub_sector_id',      // Foreign key untuk CreativeSubSector di pivot
            'profile_id',         // Primary key lokal (UserProfile)
            'sub_sector_id'       // Primary key terkait (CreativeSubSector)
        );
    }
    public function userNeeds(): BelongsToMany
    {
        // Asumsi PK UserNeed adalah need_id
        return $this->belongsToMany(
            UserNeed::class,
            'profile_need',       // Nama tabel pivot
            'user_profile_id',         // Foreign key untuk UserProfile di pivot
            'need_id',            // Foreign key untuk UserNeed di pivot
            'profile_id',         // Primary key lokal (UserProfile)
            'need_id'             // Primary key terkait (UserNeed)
        );
    }

}
