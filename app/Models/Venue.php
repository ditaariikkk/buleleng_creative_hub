<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    use HasFactory;
    protected $primaryKey = 'venue_id';
    protected $fillable = [
        'venue_name',
        'address',
        'capacity',
        'contact',
        'owner',
        'photo_path',
    ];

    /**
     * Mendefinisikan relasi One-to-Many ke Event.
     * Satu venue bisa memiliki banyak event.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'venue_id', 'venue_id');
    }
}