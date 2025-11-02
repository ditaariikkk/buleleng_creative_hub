<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id'; // Sudah benar

    protected $fillable = [
        'event_title',
        'description',
        'start_datetime',
        'end_datetime',
        'venue_id',
        'event_type',
        // Tambahkan 'image_path' jika memang ada kolom ini
        // 'image_path', 
    ];

    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class, 'venue_id'); // Asumsi PK Venue adalah 'venue_id'
    }

    /**
     * Relasi Many-to-Many ke CreativeSubSector.
     * PERBAIKAN: Menentukan semua key secara eksplisit.
     */
    public function creativeSubSectors(): BelongsToMany
    {
        return $this->belongsToMany(
            CreativeSubSector::class,
            'event_sub_sector', // Nama tabel pivot
            'event_id',         // Foreign key untuk Event di pivot
            'sub_sector_id',    // Foreign key untuk CreativeSubSector di pivot
            'event_id',         // Primary key lokal (Event)
            'sub_sector_id'     // Primary key terkait (CreativeSubSector)
        );
    }

    /**
     * Accessor untuk status event dinamis.
     */
    public function getEventStatusAttribute(): string
    {
        $now = Carbon::now();
        $startDate = Carbon::parse($this->start_datetime);
        $endDate = Carbon::parse($this->end_datetime);

        if ($now->isBefore($startDate)) {
            return 'Belum Terlaksana';
        } elseif ($now->isBetween($startDate, $endDate)) {
            return 'Sedang Berlangsung';
        } else {
            return 'Telah Berakhir';
        }
    }

    /**
     * (Opsional) Auto-set event_status saat membuat
     * Jika Anda ingin status otomatis terisi 'Belum Terlaksana'
     * dan TIDAK menggunakan accessor getEventStatusAttribute untuk menyimpan.
     */
    /*
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->event_status)) {
                $event->event_status = 'Belum Terlaksana';
            }
        });
    }
    */
}

