<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalHistory extends Model
{
    /** @use HasFactory<\Database\Factories\MedicalHistoryFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'medical_problems',
        'list_all_allergies',
        'list_all_medications',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
