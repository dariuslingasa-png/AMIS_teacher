<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MsTeamChannel extends Model
{
    protected $table = 'ms_team_channels';

    protected $fillable = [
        'ms_team_id_fk',
        'ms_channel_id',
        'display_name',
        'gender_filter',
        'learning_mode_filter',
        'is_private',
    ];

    protected $casts = ['is_private' => 'boolean'];

    public function team()
    {
        return $this->belongsTo(MsTeam::class, 'ms_team_id_fk');
    }
}
