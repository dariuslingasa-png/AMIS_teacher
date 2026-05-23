<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = [
        'name',
        'grade_level',
        'learning_mode',
        'shift',
        'gender',
        'ms_team_id',
        'ms_team_url',
    ];

    public function subjects()
    {
        return $this->hasMany(SectionSubject::class);
    }

    public function students()
    {
        return $this->hasMany(StudentSection::class);
    }

    /** Human-readable label */
    public function getDisplayNameAttribute(): string
    {
        $grade  = $this->grade_level;
        $name   = $this->name ?? 'Unnamed';
        $shift  = $this->shift ?? 'F2F';
        $gender = ucfirst($this->gender === 'male' ? 'Boys' : 'Girls');
        $year   = $this->school_year;
        return "{$grade} - {$name} {$shift} {$gender} {$year}";
    }
}
