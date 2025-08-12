<?php

namespace App\Models;

use App\Enums\MaritalStatus;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    /** @use HasFactory<\Database\Factories\PatientFactory> */
    use HasFactory;

    protected $fillable = [
        'caregiver_id',
        'last_name',
        'first_name',
        'middle_name',
        'maiden_name',
        'address',
        'city',
        'province',
        'zip',
        'birth_date',
        'birth_place',
        'phone',
        'marital_status',
        'spouse_last_name',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_maiden_name',
        'emergency_contact_name',
        'emergency_contact_relationship',
        'emergency_contact_phone',
        'guardian_name',
        'guardian_phone',
        'height',
        'weight',
        'device_identifier',
        'room',
        'bed_number'
    ];

    public function casts()
    {
        return [
            'marital_status' => MaritalStatus::class,
        ];
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id')
        ->whereIn('user_type', [UserType::Caregiver(), UserType::Sister()]);
    }

    public function sensorsValues(): HasMany
    {
        return $this->hasMany(SensorsValue::class);
    }

    public function medicalHistories(): HasMany
    {
        return $this->hasMany(MedicalHistory::class);
    }
}
