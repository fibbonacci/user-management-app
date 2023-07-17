<?php

declare(strict_types=1);

namespace App\Domain\User\Enums;

enum RestrictedDomainsEnum: string
{
    case Domain1 = 'domain1.com';
    case Domain2 = 'domain2.com';
    case Domain3 = 'domain3.com';
}
