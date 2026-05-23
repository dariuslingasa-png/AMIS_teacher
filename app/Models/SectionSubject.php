<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionSubject extends Model
{
    protected $fillable = [
        'section_id',
        'subject_name',
        'teacher_name',
        'schedule',
        'ms_channel_id',
    ];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
