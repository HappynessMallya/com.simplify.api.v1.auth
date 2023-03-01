<?php

declare(strict_types=1);

namespace App\Domain\Model\User;

use App\Domain\Model\Enum;

/**
 * Class UserStatus
 * @package App\Domain\Model\User
 *
 * @method static UserStatus ACTIVE()
 * @method static UserStatus SUSPENDED()
 * @method static UserStatus CHANGE_PASSWORD()
 */
class UserStatus extends Enum
{
    public const ACTIVE = 'ACTIVE';
    public const SUSPENDED = 'SUSPENDED';
    public const CHANGE_PASSWORD = 'CHANGE_PASSWORD';
}
