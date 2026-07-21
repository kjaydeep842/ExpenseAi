<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceiptScan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'image_url',
        'extracted_text',
        'extracted_json',
        'merchant',
        'amount',
        'gst',
        'date',
        'status',
        'transaction_id',
    ];

    protected $casts = [
        'extracted_json' => 'array',
        'amount' => 'float',
        'gst' => 'float',
        'date' => 'date',
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
