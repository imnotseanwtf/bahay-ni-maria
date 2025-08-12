<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialReport extends Model
{
    /** @use HasFactory<\Database\Factories\FinancialReportFactory> */
    use HasFactory;

     protected $fillable = [
        'date_recieved',
        'donor_name', 
        'payment_method',
        'amount',
        'remarks'
    ];
}
