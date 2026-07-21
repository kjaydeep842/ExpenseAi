<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['subscription_id', 'transaction_id', 'paid_amount', 'payment_date', 'status'];
    protected $casts = [
        'paid_amount' => 'float',
        'payment_date' => 'date',
    ];
}
