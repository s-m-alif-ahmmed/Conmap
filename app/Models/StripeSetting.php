<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StripeSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'stripe_key',
        'stripe_secret',
        'stripe_webhook_secret',
    ];
}
