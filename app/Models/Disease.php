<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disease extends Model
{
    /** @use HasFactory<\Database\Factories\DiseaseFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function patient()
    {
        return $this->belongsToMany(Patient::class, 'patient_diseases', 'disease_id', 'patient_id');
    }
}
