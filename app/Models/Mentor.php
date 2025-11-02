<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mentor extends Model
{
    use HasFactory;

    protected $primaryKey = 'mentor_id';

    protected $fillable = [
        'mentor_name',
        'bio',
        'expertise_summary',
        'mentor_contact',
        'photo_path',
        'status',
    ];


    /**
     * Relasi Many-to-Many dengan CreativeSubSector.
     */
    public function creativeSubSectors(): BelongsToMany
    {
        // belongsToMany(Model, pivot_table, foreign_pivot_key, related_pivot_key, parent_key, related_key)
        return $this->belongsToMany(
            CreativeSubSector::class,
            'mentor_sub_sector',        // Nama tabel pivot
            'mentor_id',                // FK untuk Mentor di pivot
            'creative_sub_sector_id',   // PERBAIKAN: Sesuaikan dengan nama FK *sebenarnya* di tabel pivot Anda
            'mentor_id',                // PK dari Mentor
            'sub_sector_id'             // PK dari CreativeSubSector 
        );
    }

    /**
     * Relasi Many-to-Many dengan UserNeed.
     */
    public function userNeeds(): BelongsToMany
    {
        return $this->belongsToMany(
            UserNeed::class,
            'mentor_user_need', // Nama tabel pivot
            'mentor_id',        // FK untuk Mentor di pivot
            'user_need_id',     // FK untuk UserNeed di pivot
            'mentor_id',        // PK dari Mentor 
            'need_id'           // PK dari UserNeed 
        );
    }

    /**
     * Relasi Many-to-Many ke User.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'mentor_user',      // Nama tabel pivot
            'mentor_id',        // FK untuk Mentor di pivot
            'user_id',          // FK untuk User di pivot
            'mentor_id',        // PK dari Mentor
            'user_id'           // PK dari User
        );
    }
}

