<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'link',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
