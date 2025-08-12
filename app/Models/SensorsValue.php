<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorsValue extends Model
{
    /** @use HasFactory<\Database\Factories\SensorsValueFactory> */
    use HasFactory;

    protected $fillable = [
        'bpm',
        'patient_id'
    ];

    public function patients()
    {
        return $this->belongsTo(Patient::class);
    }
}
