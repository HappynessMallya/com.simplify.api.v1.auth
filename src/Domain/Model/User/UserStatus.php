<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Enum;

/**
 * Class UserStatus
 * @package App\Domain\Model\ApiUser
 * @method static UserStatus ACTIVE()
 * @method static UserStatus SUSPENDED()
 */
final class UserStatus extends Enum
{
    public const ACTIVE = 'ACTIVE';
    public const SUSPENDED = 'SUSPENDED';
}
