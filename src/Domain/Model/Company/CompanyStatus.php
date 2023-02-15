<?php

declare(strict_types=1);

namespace App\Domain\Model\Company;

use App\Domain\Model\Enum;

/**
 * Class CompanyStatus
 * @package App\Domain\Model\Company
 * @method static CompanyStatus STATUS_ACTIVE()
 * @method static CompanyStatus STATUS_BLOCK()
 * @method static CompanyStatus STATUS_INACTIVE()
 */
class CompanyStatus extends Enum
{
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_BLOCK = 'BLOCK';
    public const STATUS_INACTIVE = 'INACTIVE';
}
