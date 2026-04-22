<?php

declare(strict_types=1);

namespace App\Enums;

enum PageType: string
{
    case Landing = 'landing';
    case Product = 'product';
    case ThankYou = 'thank_you';
    case Legal = 'legal';
}
