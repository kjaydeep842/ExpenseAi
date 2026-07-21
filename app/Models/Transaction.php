<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'bank_account_id',
        'category_id',
        'merchant_id',
        'type',
        'amount',
        'net_amount',
        'tax_amount',
        'fee_amount',
        'currency',
        'status',
        'transaction_date',
        'reference_number',
        'notes',
        'location',
        'payment_method',
        'is_recurring',
        'tags',
        'raw_sms',
        'attachment_url',
    ];

    protected $casts = [
        'amount' => 'float',
        'net_amount' => 'float',
        'tax_amount' => 'float',
        'fee_amount' => 'float',
        'transaction_date' => 'datetime',
        'is_recurring' => 'boolean',
        'tags' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function merchant(): BelongsTo
    {
        return $this->belongsTo(Merchant::class);
    }
}
