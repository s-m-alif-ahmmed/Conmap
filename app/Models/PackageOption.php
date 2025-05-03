<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PackageOption extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'package_id',
        'name',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
