<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Collaboration extends Model
{
    use HasFactory;


    protected $primaryKey = 'collaboration_id';
    /**
     * Kolom yang bisa diisi.
     */
    protected $fillable = [
        'requester_id',
        'recipient_id',
        'status',
    ];

    /**
     * Relasi: Mendapatkan user yang MENGAJUKAN kolaborasi.
     */
    public function requester()
    {
        return $this->belongsTo(User::class, 'requester_id');
    }

    /**
     * Relasi: Mendapatkan user yang MENERIMA (diajak) kolaborasi.
     */
    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}