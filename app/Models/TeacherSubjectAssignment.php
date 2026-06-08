<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeacherSubjectAssignment extends Model
{
    protected $fillable = [
        'teacher_key',
        'teacher_name',
        'teacher_email',
        'subject_id',
        'status',
        'assigned_by',
        'assigned_at',
        'ended_at',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
