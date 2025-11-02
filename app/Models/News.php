<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    /**
     * Nama tabel jika berbeda dari 'news'.
     * protected $table = 'nama_tabel_berita'; 
     */

    /**
     * Primary key kustom.
     */
    protected $primaryKey = 'news_id';

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'title',
        'news_photo', // Path foto berita
        'description',
        'source_url', // URL sumber berita
    ];
}

