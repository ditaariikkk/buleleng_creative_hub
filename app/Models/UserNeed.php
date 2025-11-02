<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

class UserNeed extends Model
{
    use HasFactory;
    protected $primaryKey = 'need_id';

    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Mendefinisikan relasi Many-to-Many ke UserProfile.
     */
    public function userProfiles(): BelongsToMany
    {
        return $this->belongsToMany(UserProfile::class, 'profile_need');
    }

    /**
     * Mendefinisikan relasi Polymorphic (inverse) ke LmsContent.
     */
    public function lmsContents(): MorphedByMany
    {
        return $this->morphedByMany(LmsContent::class, 'pivotable', 'lms_content_pivot');
    }
}