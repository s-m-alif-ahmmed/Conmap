<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
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

}
