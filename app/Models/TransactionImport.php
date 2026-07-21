<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionImport extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'file_name',
        'file_type',
        'source',
        'status',
        'total_count',
        'processed_count',
        'error_log',
    ];

    protected $casts = [
        'error_log' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
