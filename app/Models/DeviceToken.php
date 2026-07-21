<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['user_id', 'token', 'device_type', 'device_name', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
