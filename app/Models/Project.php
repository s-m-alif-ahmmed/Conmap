<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'project_type_id',
        'duration_id',
        'unit_id',
        'name',
        'address',
        'postal_code',
        'client_name',
        'description',
        'local_authority',
        'site_contact',
        'site_reference',
        'note',
        'latitude',
        'longitude',
        'land_condition',
        'end_date',
        'project_build_type',
        'land_status',
        'visited_status',
        'live_status',
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

    public function user(){
        return $this->belongsTo(User::class,'user_id');
    }

    public function projectPins()
    {
        return $this->hasMany(ProjectPin::class);
    }

    public function projectType(){
        return $this->belongsTo(ProjectType::class);
    }

    public function duration(){
        return $this->belongsTo(Duration::class);
    }

    public function unit(){
        return $this->belongsTo(Unit::class);
    }

    public function projectImages(): HasMany
    {
        return $this->hasMany(ProjectImage::class);
    }

    public function projectLinks(): HasMany
    {
        return $this->hasMany(ProjectLink::class);
    }

    public function projectContacts(): HasMany
    {
        return $this->hasMany(ProjectContact::class);
    }

}
