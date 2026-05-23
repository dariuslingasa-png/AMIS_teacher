<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['name', 'code', 'grade_level', 'school_year'];

    public function msTeam()
    {
        return $this->hasOne(MsTeam::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subjects')
                    ->withPivot('school_year')
                    ->withTimestamps();
    }
}
