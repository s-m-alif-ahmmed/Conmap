<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'email',
        'number',
        'tel_number',
        'whatsapp_number',
        'system_name',
        'address',
        'copyright_text',
        'logo',
        'favicon',
        'description',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function getLogoAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

    public function getFaviconAttribute($value){
        if (filter_var($value, FILTER_VALIDATE_URL)) {
            return $value;
        }
        if (request()->is('api/*') && !empty($value)) {
            return url($value);
        }
        return $value;
    }

}
