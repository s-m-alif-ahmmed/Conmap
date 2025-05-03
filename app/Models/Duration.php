<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duration extends Model
{
    use HasFactory;

    protected $fillable = [
        'duration',
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

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

}
