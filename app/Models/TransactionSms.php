<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionSms extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'transaction_sms';

    protected $fillable = [
        'user_id',
        'raw_body',
        'sender',
        'amount',
        'type',
        'merchant',
        'bank',
        'ref_no',
        'parsed_status',
        'transaction_id',
    ];

    protected $casts = [
        'amount' => 'float',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
