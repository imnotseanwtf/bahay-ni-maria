<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealtimeLocation extends Model
{
    /** @use HasFactory<\Database\Factories\RealtimeLocationFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'latitude',
        'longitude'
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
