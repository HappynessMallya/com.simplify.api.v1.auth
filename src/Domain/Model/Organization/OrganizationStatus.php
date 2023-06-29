<?php

declare(strict_types=1);

namespace App\Domain\Model\Organization;

use App\Domain\Model\Enum;

/**
 * Class OrganizationStatus
 * @package App\Domain\Model\Organization
 *
 * @method static OrganizationStatus ACTIVE()
 * @method static OrganizationStatus INACTIVE()
 */
class OrganizationStatus extends Enum
{
    public const ACTIVE = 'ACTIVE';
    public const INACTIVE = 'INACTIVE';
}
