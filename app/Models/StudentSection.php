<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSection extends Model
{
    protected $fillable = ['student_id', 'section_id', 'school_year'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
