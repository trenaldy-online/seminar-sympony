<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable =  [
                            'name',
                            'slug',
                            'description',
                            'date',
                            'is_active',
                            'banner_image',
                            'custom_fields_config',
                            'max_capacity',
                            'is_paid',
                            'price',
                            'bank_name',
                            'account_number',
                            'account_holder',
                        ];

    /**
     * Relasi One-to-Many: Satu Event memiliki banyak Participant
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    protected $casts = [
        'is_active' => 'boolean',
        'custom_fields_config' => 'array',
        'is_paid' => 'boolean',
    ];

}
