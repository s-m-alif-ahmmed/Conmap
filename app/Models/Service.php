<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'icon',
        'title',
        'description',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public static function published()
    {
        return self::query()->where('status', 'Active');
    }

    public function getIconAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

}
