<?php

declare(strict_types=1);

namespace App\Enums;

enum CrmStatus: string
{
    case Pending = 'pending';
    case Synced = 'synced';
    case Failed = 'failed';
}
