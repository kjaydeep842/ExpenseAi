<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'category_id', 'merchant_id', 'amount', 'frequency', 'next_run_date', 'is_active'];
    protected $casts = [
        'amount' => 'float',
        'next_run_date' => 'date',
        'is_active' => 'boolean',
    ];
}
