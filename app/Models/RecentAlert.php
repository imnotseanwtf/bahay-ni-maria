<?php

namespace App\Models;

use App\Enums\AlertType;
use App\Observers\RecentAlertObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([RecentAlertObserver::class])]
class RecentAlert extends Model
{
    /** @use HasFactory<\Database\Factories\RecentAlertFactory> */
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'alert_type',
        'bpm',
        'caregiver_id',
    ];

    protected $casts = [
        'alert_type' => AlertType::class
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function caregiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'caregiver_id');
    }
}
