<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'status',
        'archived_at',
        'grade_level',
        'school_year',
    ];

    protected function casts(): array
    {
        return ['archived_at' => 'datetime'];
    }
}
