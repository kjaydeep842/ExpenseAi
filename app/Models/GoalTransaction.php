<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoalTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['goal_id', 'user_id', 'transaction_id', 'amount', 'note', 'type'];
    protected $casts = ['amount' => 'float'];

    public function goal(): BelongsTo
    {
        return $this->belongsTo(Goal::class);
    }
}
