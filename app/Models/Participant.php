<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Kolom yang diizinkan untuk diisi massal (melalui Participant::create()).
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'qr_code_token', // Penting: Ini harus diizinkan untuk diisi
        'is_checked_in',
    ];

    /**
     * The attributes that should be cast to native types.
     * Mengatur casting tipe data.
     * @var array
     */
    protected $casts = [
        'is_checked_in' => 'boolean', // Pastikan ini dicast sebagai boolean
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
