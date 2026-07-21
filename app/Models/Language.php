<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory, HasUuids;
    protected $fillable = ['code', 'name', 'flag', 'is_default'];
    protected $casts = ['is_default' => 'boolean'];
}
