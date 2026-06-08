<?php

namespace App\Enums;

enum MeetingStatus: string
{
    case SCHEDULED = 'Scheduled';
    case LIVE = 'Live';
    case DRAFT = 'Draft';
    case COMPLETED = 'Completed';
}
