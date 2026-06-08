<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['name', 'grade_level', 'learning_mode', 'shift', 'gender'];

    public function subjects()
    {
        return $this->hasMany(SectionSubject::class);
    }

    public function students()
    {
        return $this->hasMany(StudentSection::class);
    }

    public function getSectionTitleAttribute(): string
    {
        return trim(($this->grade_level ?: 'Class').' - '.($this->name ?: 'General'));
    }
}
