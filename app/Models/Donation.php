<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    /** @use HasFactory<\Database\Factories\DonationFactory> */
    use HasFactory;

    protected $fillable = [
        'date_recieved',
        'donor_name', 
        'item_description',
        'quantity',
        'expiration_date',
        'remarks'
    ];
}
