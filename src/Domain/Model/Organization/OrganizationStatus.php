<?php

declare(strict_types=1);

namespace App\Domain\Model\Organization;

use App\Domain\Model\Enum;

/**
 * Class OrganizationStatus
 * @package App\Domain\Model\Organization
 *
 * @method static OrganizationStatus STATUS_ACTIVE()
 * @method static OrganizationStatus STATUS_BLOCK()
 * @method static OrganizationStatus STATUS_INACTIVE()
 */
class OrganizationStatus extends Enum
{
    public const STATUS_ACTIVE = 'ACTIVE';
    public const STATUS_BLOCK = 'BLOCK';
    public const STATUS_INACTIVE = 'INACTIVE';
}
