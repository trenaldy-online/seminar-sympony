<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = ['name', 'slug', 'description', 'date', 'is_active'];

    /**
     * Relasi One-to-Many: Satu Event memiliki banyak Participant
     */
    public function participants()
    {
        return $this->hasMany(Participant::class);
    }
}
