<?php

declare(strict_types=1);

namespace App\Enums;

enum JourneyStatus: string
{
    case Qualifying = 'qualifying';
    case Routed = 'routed';
    case Submitted = 'submitted';
    case Expired = 'expired';
}
