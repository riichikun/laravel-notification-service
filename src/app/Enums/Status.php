<?php

declare(strict_types=1);

namespace App\Enums;

enum Status: string
{
    case QUEUED = 'queued';
    case SENT = 'sent';
    case DELIVERED = 'delivered';
    case FAILED = 'failed';
}
