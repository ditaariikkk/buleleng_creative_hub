<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphedByMany;

class CreativeSubSector extends Model
{
    use HasFactory;

    protected $primaryKey = 'sub_sector_id'; // Sudah Benar

    protected $fillable = [
        'name',
        'description',
    ];

    public function mentors(): BelongsToMany
    {
        // PERBAIKAN: Gunakan 'sub_sector_id' sebagai foreign key di pivot
        // Menentukan semua key secara eksplisit untuk kejelasan
        return $this->belongsToMany(
            Mentor::class,
            'mentor_sub_sector',        // Nama tabel pivot
            'sub_sector_id',            // Foreign key untuk CreativeSubSector di pivot
            'mentor_id',                // Foreign key untuk Mentor di pivot
            'sub_sector_id',            // Primary key lokal (CreativeSubSector)
            'mentor_id'                 // Primary key terkait (Mentor)
        );
    }

    public function userProfiles(): BelongsToMany
    {
        // PERBAIKAN: Gunakan 'sub_sector_id' sebagai foreign key di pivot
        // Menentukan semua key secara eksplisit untuk kejelasan
        return $this->belongsToMany(
            UserProfile::class,
            'profile_sub_sector',       // Nama tabel pivot
            'sub_sector_id',            // Foreign key untuk CreativeSubSector di pivot
            'profile_id',               // Foreign key untuk UserProfile di pivot (Asumsi PK UserProfile adalah profile_id)
            'sub_sector_id',            // Primary key lokal (CreativeSubSector)
            'profile_id'                // Primary key terkait (UserProfile)
        );
    }

    public function events(): BelongsToMany
    {
        // PERBAIKAN: Tentukan foreign key secara eksplisit jika non-standar
        // Menentukan semua key secara eksplisit untuk kejelasan
        return $this->belongsToMany(
            Event::class,
            'event_sub_sector',         // Nama tabel pivot
            'sub_sector_id',            // Foreign key untuk CreativeSubSector di pivot
            'event_id',                 // Foreign key untuk Event di pivot (Asumsi PK Event adalah event_id)
            'sub_sector_id',            // Primary key lokal (CreativeSubSector)
            'event_id'                  // Primary key terkait (Event)
        );
    }

    public function lmsContents(): MorphedByMany
    {
        // Relasi polimorfik biasanya tidak perlu penyesuaian key ini
        return $this->morphedByMany(LmsContent::class, 'pivotable', 'lms_content_pivot');
    }
}

