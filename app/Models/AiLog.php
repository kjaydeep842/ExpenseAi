<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'prompt', 'response', 'model', 'token_count', 'execution_time_ms'];
}
