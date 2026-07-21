<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['budget_id', 'period_start', 'period_end', 'allocated_amount', 'spent_amount'];
    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'allocated_amount' => 'float',
        'spent_amount' => 'float',
    ];
}
