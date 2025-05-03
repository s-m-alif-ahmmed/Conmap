<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'description',
        'price',
        'type',
        'duration',
        'stripe_product_id',
        'stripe_price_id',
        'interval',
        'trial_days',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function published()
    {
        return self::query()->where('status', 'Active');
    }

    public function packageOptions()
    {
        return $this->hasMany(PackageOption::class);
    }

    // Corrected: A package has many subscriptions
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class, 'package_id', 'id');
    }
}
