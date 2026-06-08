<?php

namespace App\Enums;

enum AccountStatus: string
{
    case VERIFIED = 'verified';
    case PENDING = 'pending';
    case SUSPENDED = 'suspended';
}
