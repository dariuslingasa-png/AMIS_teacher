<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSection extends Model
{
    protected $fillable = [
        'student_id',
        'section_id',
        'ms_enrolled_at',
        'ms_status',
    ];

    protected $casts = ['ms_enrolled_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
