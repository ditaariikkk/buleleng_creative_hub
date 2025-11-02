<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terkait dengan model.
     * (Opsional jika nama tabel adalah bentuk jamak dari nama model, misal 'products')
     * protected $table = 'product'; 
     */

    /**
     * Primary key kustom.
     */
    protected $primaryKey = 'product_id';

    /**
     * Atribut yang dapat diisi secara massal.
     * Sesuaikan dengan nama kolom di database Anda.
     */
    protected $fillable = [
        'product_name',
        'owner',
        'contact',
        'address',
        'photo_path',
        'description',
    ];
}