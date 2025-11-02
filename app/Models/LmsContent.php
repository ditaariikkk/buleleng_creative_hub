<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsContent extends Model
{
    use HasFactory;

    protected $primaryKey = 'content_id';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content_title',
        'description',
        'type',
        'source',
        'sub_sector_id',
    ];

    /**
     * Definisikan relasi BelongsTo ke CreativeSubSector.
     */
    public function creativeSubSector(): BelongsTo
    {
        return $this->belongsTo(CreativeSubSector::class, 'sub_sector_id', 'sub_sector_id');
    }

}

