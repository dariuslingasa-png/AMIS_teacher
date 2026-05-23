<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMsTeam extends Model
{
    protected $table = 'student_ms_teams';

    protected $fillable = [
        'student_id',
        'ms_team_id_fk',
        'ms_channel_id_fk',
        'enrolled_at',
        'status',
        'error_message',
    ];

    protected $casts = ['enrolled_at' => 'datetime'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function team()
    {
        return $this->belongsTo(MsTeam::class, 'ms_team_id_fk');
    }

    public function channel()
    {
        return $this->belongsTo(MsTeamChannel::class, 'ms_channel_id_fk');
    }
}
