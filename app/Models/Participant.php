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
        'event_id',
        'name',
        'email',
        'phone',
        'qr_code_token',
        'is_checked_in', // DIPERBAIKI: Harus ada di fillable jika di-set di controller
        'custom_fields_data',
        'nik',
        'is_paid',
        'unique_code',
    ];

    /**
     * The attributes that should be cast to native types.
     * Mengatur casting tipe data.
     * @var array
     */
    protected $casts = [
        'is_checked_in' => 'boolean', // Pastikan ini dicast sebagai boolean
        'custom_fields_data' => 'array',
        'is_paid' => 'boolean',
        'checked_in_at' => 'datetime', // Casting untuk kolom timestamp check-in
    ];

    /**
     * Relasi: Satu Participant dimiliki oleh satu Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
