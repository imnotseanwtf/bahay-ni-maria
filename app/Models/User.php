<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'mobile_number',
        'user_type'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'user_type' => UserType::class
        ];
    }

    public function isAdmin(): bool
    {
        return $this->user_type == UserType::Admin();
    }

    public function isCaregiver(): bool
    {
        $type = $this->user_type;

        if ($type instanceof \App\Enums\UserType) {
            return $type->isOneOf([\App\Enums\UserType::Caregiver, \App\Enums\UserType::Sister]);
        }

        return in_array($type, [\App\Enums\UserType::Caregiver, \App\Enums\UserType::Sister], true);
    }
}
